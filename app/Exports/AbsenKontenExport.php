<?php
namespace App\Exports;

use App\Models\absenkonten;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsenKontenExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;
    protected $userRuanganIds;

    public function __construct($request, $userRuanganIds)
    {
        $this->request = $request;
        $this->userRuanganIds = $userRuanganIds;
    }

    public function query()
    {
        $query = absenkonten::with(['pegawai', 'ruangan', 'verifier'])
            ->whereIn('id_ruangan', $this->userRuanganIds);

        // Filter Tanggal
        if ($this->request->start_date && $this->request->end_date) {
            $query->whereBetween('tanggal', [
                $this->request->start_date,
                $this->request->end_date
            ]);
        }

        // Filter Ruangan (Opsional)
        if ($this->request->id_ruangan) {
            $query->where('id_ruangan', $this->request->id_ruangan);
        }

        // Filter Status Verifikasi (Opsional)
        if ($this->request->status_verifikasi) {
            $query->where('status_verifikasi', $this->request->status_verifikasi);
        }

        return $query->latest('tanggal');
    }

    // Header Kolom di Excel
    public function headings(): array
    {
        return [
            'No',
            'Nama Pegawai',
            'Ruangan',
            'Tanggal',
            'Link FB',
            'Link IG',
            'Link TikTok',
            'Keterangan',
            'Status Verifikasi',
            'Diverifikasi Oleh',
        ];
    }

    // Pemetaan Data Per Baris
    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->pegawai->name ?? '-',
            $row->ruangan->nama_ruangan ?? '-',
            \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y'),
            $row->link_fb ?? '-',
            $row->link_ig ?? '-',
            $row->link_tiktok ?? '-',
            $row->keterangan ?? '-',
            ucfirst($row->status_verifikasi),
            $row->verifier->name ?? 'Belum diverifikasi',
        ];
    }

    // Styling Header Excel
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4E73DF'] // Warna header biru
                ],
            ],
        ];
    }
}
