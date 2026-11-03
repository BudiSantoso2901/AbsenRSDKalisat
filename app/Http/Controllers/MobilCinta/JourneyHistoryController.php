<?php

namespace App\Http\Controllers\MobilCinta;

use App\Http\Controllers\Controller;
use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Journey;
use App\Models\MobilCinta\Vehicle;
use App\Models\Ruangan;
use App\Services\MobilCinta\JourneyListQueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JourneyHistoryController extends Controller
{
    public function index(
        Request $request,
        JourneyListQueryService $query
    ): View {
        /** @var Account $account */
        $account = Auth::guard('mobil_cinta')->user();

        $role = $account->role?->nama_peran;

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => [
                'nullable',
                'in:diajukan,ditugaskan,berangkat,selesai,ditolak,dibatalkan'
            ],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        if (
            !empty($validated['from']) &&
            !empty($validated['to']) &&
            $validated['from'] > $validated['to']
        ) {
            return back()
                ->withErrors([
                    'to' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.',
                ])
                ->withInput();
        }

        $filters = [
            'q' => trim($validated['q'] ?? ''),
            'status' => $validated['status'] ?? '',
            'from' => $validated['from'] ?? '',
            'to' => $validated['to'] ?? '',
        ];

        /*
         * Ringkasan perjalanan.
         */
        $summaryFilters = $filters;
        unset($summaryFilters['status']);

        $summaryQuery = $query->forAccount(
            $account,
            $summaryFilters
        );

        $summary = [
            'total' => (clone $summaryQuery)->count(),

            'diajukan' => (clone $summaryQuery)
                ->where('status', Journey::STATUS_REQUESTED)
                ->count(),

            'proses' => (clone $summaryQuery)
                ->whereIn('status', [
                    Journey::STATUS_ASSIGNED,
                    Journey::STATUS_DEPARTED,
                ])
                ->count(),

            'selesai' => (clone $summaryQuery)
                ->where('status', Journey::STATUS_COMPLETED)
                ->count(),
        ];

        /*
         * Daftar perjalanan.
         */
        $journeys = $query
            ->forAccount($account, $filters)
            ->latest('diajukan_pada')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        /*
         * Data pendukung modal Perawat dan Koordinator.
         */
        $rooms = collect();
        $drivers = collect();
        $vehicles = collect();

        if ($role === 'perawat') {
            $rooms = Ruangan::query()
                ->orderBy('nama_ruangan')
                ->get(['id', 'nama_ruangan']);
        }

        if ($role === 'koordinator') {
            $drivers = Account::query()
                ->whereHas('role', function ($builder) {
                    $builder->where('nama_peran', 'sopir');
                })
                ->where('status_aktif', true)
                ->orderBy('nama')
                ->get(['id', 'nama']);

            $vehicles = Vehicle::query()
                ->orderBy('nomor_polisi')
                ->get(['id', 'nomor_polisi']);
        }

        return view('mobil-cinta.riwayat.index', [
            'journeys' => $journeys,
            'filters' => $filters,
            'summary' => $summary,
            'rooms' => $rooms,
            'drivers' => $drivers,
            'vehicles' => $vehicles,
        ]);
    }
}