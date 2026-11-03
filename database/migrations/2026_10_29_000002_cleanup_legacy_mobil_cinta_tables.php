<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    private const LEGACY = [
        'mobil_cinta_status_histories',
        'mobil_cinta_journeys',
        'mobil_cinta_assignments',
        'mobil_cinta_trip_requests',
        'mobil_cinta_accounts',
        'mobil_cinta_roles',
        'mobil_cinta_vehicles',
        'mobil_cinta_guarantors',
    ];

    public function up(): void
    {
        if (DB::connection()->pretending()) {
            return;
        }

        foreach (['mobil_cinta_peran', 'mobil_cinta_akun', 'mobil_cinta_kendaraan', 'mobil_cinta_perjalanan'] as $table) {
            if (!Schema::hasTable($table)) {
                throw new RuntimeException('Tabel target Mobil Cinta tidak lengkap; cleanup dibatalkan.');
            }
        }

        foreach (self::LEGACY as $table) {
            if (!Schema::hasTable($table)) {
                throw new RuntimeException("Tabel sumber {$table} tidak ditemukan; cleanup dibatalkan tanpa menghapus tabel.");
            }
        }

        if (Schema::hasTable('mobil_cinta_riwayat_aktivitas')) {
            $count = DB::table('mobil_cinta_riwayat_aktivitas')->count();
            if ($count > 0) {
                throw new RuntimeException("Cleanup Mobil Cinta dibatalkan: mobil_cinta_riwayat_aktivitas berisi {$count} baris.");
            }
        }

        $this->assertRowsMatch('mobil_cinta_roles', 'mobil_cinta_peran', 'id', function ($source, $target) {
            return $this->normalizeRole($source->name) === $target->nama_peran;
        });
        $this->assertRowsMatch(
            'mobil_cinta_accounts',
            'mobil_cinta_akun',
            'id',
            fn($s, $t) =>
                (int) $s->role_id === (int) $t->peran_id
                && $s->name === $t->nama
                && $s->username === $t->nama_pengguna
                && hash_equals((string) $s->password, (string) $t->kata_sandi)
                && (bool) $s->is_active === (bool) $t->status_aktif
        );
        $this->assertRowsMatch('mobil_cinta_vehicles', 'mobil_cinta_kendaraan', 'id', fn($s, $t) => $s->plate_number === $t->nomor_polisi);

        // Operational source rows are not safely reducible to one journey when assignments/history repeat.
        // Require these tables to be empty until an explicit verified mapping is prepared.
        foreach (['mobil_cinta_guarantors', 'mobil_cinta_trip_requests', 'mobil_cinta_assignments', 'mobil_cinta_journeys', 'mobil_cinta_status_histories'] as $table) {
            $count = DB::table($table)->count();
            if ($count > 0) {
                throw new RuntimeException("Cleanup Mobil Cinta dibatalkan: {$table} memiliki {$count} baris yang belum memiliki pemeriksaan pemetaan aman.");
            }
        }

        // Child-to-parent order; Absensi tables, ruangans, and migrations are untouched.
        foreach (['mobil_cinta_status_histories', 'mobil_cinta_journeys', 'mobil_cinta_assignments', 'mobil_cinta_trip_requests', 'mobil_cinta_guarantors', 'mobil_cinta_vehicles', 'mobil_cinta_accounts', 'mobil_cinta_roles'] as $table) {
            Schema::drop($table);
        }

        if (Schema::hasTable('mobil_cinta_riwayat_aktivitas')) {
            Schema::drop('mobil_cinta_riwayat_aktivitas');
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Cleanup legacy Mobil Cinta tidak dapat di-rollback: data dan struktur tabel sumber telah dihapus. Pulihkan dari backup yang diverifikasi.');
    }

    private function assertRowsMatch(string $sourceTable, string $targetTable, string $idColumn, callable $matches): void
    {
        if (DB::table($sourceTable)->count() !== DB::table($targetTable)->count()) {
            throw new RuntimeException("Cleanup Mobil Cinta dibatalkan: jumlah baris {$sourceTable} dan {$targetTable} berbeda.");
        }
        foreach (DB::table($sourceTable)->orderBy($idColumn)->cursor() as $source) {
            $target = DB::table($targetTable)->where('id', $source->$idColumn)->first();
            if (!$target || !$matches($source, $target)) {
                throw new RuntimeException("Cleanup Mobil Cinta dibatalkan: pemetaan data {$sourceTable} tidak cocok dengan target.");
            }
        }
    }

    private function normalizeRole(string $role): string
    {
        return match (strtolower(trim($role))) {
            'administrator' => 'admin', 'coordinator' => 'koordinator', 'nurse' => 'perawat', 'driver' => 'sopir',
            default => strtolower(trim($role)),
        };
    }
};
