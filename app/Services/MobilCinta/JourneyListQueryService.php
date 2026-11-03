<?php

namespace App\Services\MobilCinta;

use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Journey;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;

class JourneyListQueryService
{
    /**
     * Mengambil daftar perjalanan berdasarkan hak akses akun
     * sekaligus menerapkan filter pencarian.
     */
    public function forAccount(
        Account $account,
        array $filters = []
    ): Builder {
        $role = $account->role?->nama_peran;

        $query = Journey::query()->with([
            'requester:id,nama',
            'driver:id,nama',
            'vehicle:id,nomor_polisi',
            'room:id,nama_ruangan',
        ]);

        /*
         * Aturan akses berdasarkan peran.
         *
         * Admin       : seluruh perjalanan
         * Perawat     : seluruh perjalanan
         * Koordinator : seluruh perjalanan
         * Sopir       : perjalanan yang ditugaskan kepadanya
         */
        if (in_array($role, ['admin', 'perawat', 'koordinator'], true)) {
            // Tidak perlu pembatasan tambahan.
        } elseif ($role === 'sopir') {
            $query->where('sopir_id', $account->getKey());
        } else {
            throw new AuthorizationException(
                'Role ini tidak memiliki akses ke daftar perjalanan Mobil Cinta.'
            );
        }

        /*
         * Filter pencarian.
         */
        $keyword = trim($filters['q'] ?? '');

        if ($keyword !== '') {
            $query->where(function (Builder $search) use ($keyword) {
                $search->where(
                    'nomor_perjalanan',
                    'like',
                    "%{$keyword}%"
                )
                ->orWhere(
                    'nama_pasien',
                    'like',
                    "%{$keyword}%"
                )
                ->orWhere(
                    'nomor_rekam_medis',
                    'like',
                    "%{$keyword}%"
                )
                ->orWhere(
                    'tujuan',
                    'like',
                    "%{$keyword}%"
                )
                ->orWhere(
                    'nama_sopir',
                    'like',
                    "%{$keyword}%"
                )
                ->orWhereHas('driver', function (Builder $driver) use ($keyword) {
                    $driver->where('nama', 'like', "%{$keyword}%");
                });
            });
        }

        /*
         * Filter status.
         */
        $status = $filters['status'] ?? '';

        if ($status !== '') {
            $query->where('status', $status);
        }

        /*
         * Filter tanggal berdasarkan waktu pengajuan.
         * Menggunakan created_at sebagai cadangan apabila
         * diajukan_pada bernilai NULL.
         */
        $from = $filters['from'] ?? '';
        $to = $filters['to'] ?? '';

        if ($from !== '') {
            $query->whereRaw(
                'DATE(COALESCE(diajukan_pada, created_at)) >= ?',
                [$from]
            );
        }

        if ($to !== '') {
            $query->whereRaw(
                'DATE(COALESCE(diajukan_pada, created_at)) <= ?',
                [$to]
            );
        }

        return $query;
    }
}