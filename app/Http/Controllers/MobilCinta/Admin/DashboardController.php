<?php

namespace App\Http\Controllers\MobilCinta\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobilCinta\Journey;
use App\Models\MobilCinta\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman laporan dan monitoring perjalanan.
     */
    public function __invoke(Request $request): View
    {
        $data = $this->buildReportData($request);

        return view('mobil-cinta.admin.dashboard', $data);
    }

    /**
     * Export laporan dalam format CSV yang kompatibel dengan Excel.
     */
    public function exportExcel(Request $request)
    {
        $data = $this->buildReportData($request);

        $filename = 'laporan-perjalanan-'
            . $data['from'] . '-'
            . $data['to'] . '.csv';

        return response()->streamDownload(function () use ($data) {
            $output = fopen('php://output', 'w');

            // BOM agar karakter Indonesia terbaca baik di Excel.
            fwrite($output, "\xEF\xBB\xBF");

            // Pemisah titik koma sesuai regional Excel Indonesia.
            fputcsv($output, [
                'No.',
                'Nomor Perjalanan',
                'Tanggal Pengajuan',
                'Nama Pasien',
                'Tujuan',
                'Penjamin',
                'Sopir',
                'Nomor Polisi',
                'Status',
                'KM Berangkat',
                'KM Sampai',
                'Jarak (KM)',
                'Waktu Berangkat',
                'Waktu Sampai',
            ], ';', '"', '\\');

            foreach ($data['journeys'] as $index => $journey) {
                $distance = $this->journeyDistance($journey);

                $submittedAt = $journey->diajukan_pada
                    ?? $journey->created_at;

                $row = [
                    $index + 1,
                    $journey->nomor_perjalanan,
                    $submittedAt
                        ? Carbon::parse($submittedAt)->format('d/m/Y H:i')
                        : '',
                    $journey->nama_pasien,
                    $journey->tujuan,
                    $journey->penjamin,
                    $journey->driver?->nama ?? '',
                    $journey->vehicle?->nomor_polisi ?? '',
                    $data['statusLabels'][$journey->status]
                        ?? ucfirst($journey->status),
                    $journey->kilometer_berangkat,
                    $journey->kilometer_sampai,
                    $distance !== null ? number_format($distance, 2, '.', '') : '',
                    $journey->waktu_berangkat
                        ? Carbon::parse($journey->waktu_berangkat)->format('d/m/Y H:i')
                        : '',
                    $journey->waktu_sampai
                        ? Carbon::parse($journey->waktu_sampai)->format('d/m/Y H:i')
                        : '',
                ];

                $row = array_map(
                    fn ($value) => $this->safeCsvValue($value),
                    $row
                );

                fputcsv($output, $row, ';', '"', '\\');
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Halaman siap cetak untuk disimpan sebagai PDF.
     */
    public function exportPdf(Request $request): View
    {
        $data = $this->buildReportData($request);

        return view('mobil-cinta.admin.dashboard-pdf', $data);
    }

    /**
     * Mengambil dan memvalidasi rentang tanggal.
     */
    private function resolveDateRange(Request $request): array
    {
        $validated = Validator::make($request->query(), [
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ])->validate();

        $from = $validated['from']
            ?? now()->startOfMonth()->toDateString();

        $to = $validated['to']
            ?? now()->toDateString();

        if ($from > $to) {
            throw ValidationException::withMessages([
                'to' => 'Tanggal sampai tidak boleh lebih awal dari tanggal mulai.',
            ]);
        }

        return [$from, $to];
    }

    /**
     * Query perjalanan sesuai periode.
     * Tanggal pengajuan menjadi dasar filter.
     */
    private function journeysQuery(string $from, string $to): Builder
    {
        return Journey::query()
            ->whereRaw(
                'DATE(COALESCE(diajukan_pada, created_at)) BETWEEN ? AND ?',
                [$from, $to]
            );
    }

    /**
     * Menghitung jarak perjalanan berdasarkan odometer.
     * Nilai null berarti kilometer belum lengkap.
     */
    private function journeyDistance(Journey $journey): ?float
    {
        if (
            $journey->kilometer_berangkat === null ||
            $journey->kilometer_sampai === null
        ) {
            return null;
        }

        $start = (float) $journey->kilometer_berangkat;
        $end = (float) $journey->kilometer_sampai;

        if ($end < $start) {
            return null;
        }

        return $end - $start;
    }

    /**
     * Label status yang digunakan sistem Mobil Cinta.
     */
    private function statusDefinitions(): array
    {
        return [
            'diajukan' => [
                'label' => 'Diajukan',
                'color' => '#91CDA8',
            ],
            'ditugaskan' => [
                'label' => 'Ditugaskan',
                'color' => '#399F70',
            ],
            'berangkat' => [
                'label' => 'Berangkat',
                'color' => '#08A653',
            ],
            'selesai' => [
                'label' => 'Selesai',
                'color' => '#60B9DC',
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'color' => '#D97878',
            ],
            'dibatalkan' => [
                'label' => 'Dibatalkan',
                'color' => '#9AA7A0',
            ],
        ];
    }

    /**
     * Menyiapkan seluruh data dashboard dan laporan.
     */
    private function buildReportData(Request $request): array
    {
        [$from, $to] = $this->resolveDateRange($request);

        $baseQuery = $this->journeysQuery($from, $to);

        /*
        |--------------------------------------------------------------------------
        | DATA PERJALANAN
        |--------------------------------------------------------------------------
        */

        $journeys = (clone $baseQuery)
            ->with(['driver', 'vehicle'])
            ->orderByRaw('COALESCE(diajukan_pada, created_at) ASC')
            ->orderBy('id')
            ->get();

        $totalJourneys = $journeys->count();

        /*
        |--------------------------------------------------------------------------
        | TOTAL KILOMETER
        |--------------------------------------------------------------------------
        */

        $totalKilometers = $journeys->sum(function ($journey) {
            return $this->journeyDistance($journey) ?? 0;
        });

        /*
        |--------------------------------------------------------------------------
        | SEDANG BERJALAN
        |--------------------------------------------------------------------------
        */

        $inProgress = $journeys
            ->where('status', 'berangkat')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | GRAFIK JUMLAH PERJALANAN PER TANGGAL
        |--------------------------------------------------------------------------
        */

        $dailyRows = (clone $baseQuery)
            ->selectRaw(
                'DATE(COALESCE(diajukan_pada, created_at)) AS tanggal,
                 COUNT(*) AS total'
            )
            ->groupByRaw(
                'DATE(COALESCE(diajukan_pada, created_at))'
            )
            ->orderBy('tanggal')
            ->get();

        $dailyData = $dailyRows->map(function ($row) {
            return [
                'tanggal' => $row->tanggal,
                'label' => Carbon::parse($row->tanggal)->format('d-m'),
                'total' => (int) $row->total,
            ];
        })->values()->all();

        $maxDailyJourneys = max(
            1,
            (int) collect($dailyData)->max('total')
        );

        /*
        |--------------------------------------------------------------------------
        | DISTRIBUSI STATUS
        |--------------------------------------------------------------------------
        */

        $statusDefinitions = $this->statusDefinitions();

        $statusCounts = $journeys
            ->countBy('status');

        $statusData = [];

        foreach ($statusDefinitions as $key => $definition) {
            $statusData[] = [
                'key' => $key,
                'label' => $definition['label'],
                'color' => $definition['color'],
                'total' => (int) ($statusCounts[$key] ?? 0),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DONUT CHART STATUS
        |--------------------------------------------------------------------------
        */

        $donutSegments = [];
        $currentPercentage = 0;

        if ($totalJourneys > 0) {
            foreach ($statusData as $item) {
                if ($item['total'] === 0) {
                    continue;
                }

                $percentage = (
                    $item['total'] / $totalJourneys
                ) * 100;

                $nextPercentage = $currentPercentage + $percentage;

                $donutSegments[] = $item['color']
                    . ' ' . round($currentPercentage, 3) . '%'
                    . ' ' . round($nextPercentage, 3) . '%';

                $currentPercentage = $nextPercentage;
            }
        }

        $statusDonutBackground = $totalJourneys > 0
            ? 'conic-gradient(' . implode(', ', $donutSegments) . ')'
            : '#EDF5EF';

        $statusLabels = [];

        foreach ($statusDefinitions as $key => $definition) {
            $statusLabels[$key] = $definition['label'];
        }

        /*
        |--------------------------------------------------------------------------
        | PENGGUNAAN KENDARAAN
        |--------------------------------------------------------------------------
        | Grafik yang sudah ada tetap dipertahankan.
        */

        $vehicleUsage = $journeys
            ->filter(fn ($journey) => $journey->kendaraan_id !== null)
            ->groupBy('kendaraan_id')
            ->map(function ($items) {
                return [
                    'nomor_polisi' => $items->first()->vehicle?->nomor_polisi
                        ?? 'Tidak diketahui',
                    'total' => $items->count(),
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values()
            ->all();

        $maxVehicleUsage = max(
            1,
            (int) collect($vehicleUsage)->max('total')
        );

        $totalVehicles = Vehicle::count();

        return compact(
            'from',
            'to',
            'journeys',
            'totalJourneys',
            'totalKilometers',
            'inProgress',
            'dailyData',
            'maxDailyJourneys',
            'statusData',
            'statusDonutBackground',
            'statusLabels',
            'vehicleUsage',
            'maxVehicleUsage',
            'totalVehicles'
        );
    }

    /**
     * Mencegah nilai teks CSV dibaca Excel sebagai formula.
     */
    private function safeCsvValue($value): string
    {
        $value = (string) ($value ?? '');

        if (preg_match('/^[\s]*[=+\-@]/u', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}