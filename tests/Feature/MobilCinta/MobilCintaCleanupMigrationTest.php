<?php

namespace Tests\Feature\MobilCinta;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Journey;
use App\Models\MobilCinta\Role;

class MobilCintaCleanupMigrationTest extends MobilCintaTestCase
{
    public function test_cleanup_drops_only_legacy_mobil_cinta_tables_when_preflight_passes(): void
    {
        $this->runCleanupMigration();
        foreach ([
            'mobil_cinta_status_histories', 'mobil_cinta_journeys', 'mobil_cinta_assignments', 'mobil_cinta_trip_requests',
            'mobil_cinta_accounts', 'mobil_cinta_roles', 'mobil_cinta_vehicles', 'mobil_cinta_guarantors', 'mobil_cinta_riwayat_aktivitas',
        ] as $table) {
            $this->assertFalse(Schema::hasTable($table), $table.' should be removed after a successful preflight.');
        }
        foreach (['users', 'pegawai', 'absensi', 'absenkontens', 'ruangans', 'migrations', 'mobil_cinta_peran', 'mobil_cinta_akun', 'mobil_cinta_kendaraan', 'mobil_cinta_perjalanan'] as $table) {
            $this->assertTrue(Schema::hasTable($table), $table.' must remain.');
        }
    }

    public function test_cleanup_aborts_before_dropping_anything_when_activity_rows_exist(): void
    {
        $role = Role::query()->create(['nama_peran' => 'admin']);
        $account = Account::query()->create(['peran_id' => $role->id, 'nama' => 'Admin', 'nama_pengguna' => 'admin.cleanup', 'kata_sandi' => 'secret', 'status_aktif' => true]);
        $journey = Journey::query()->create(['nomor_perjalanan' => 'MC-CLEANUP', 'diajukan_oleh_id' => $account->id, 'nama_pasien' => 'Test', 'nomor_rekam_medis' => 'RM', 'penjamin' => 'Umum', 'tujuan' => 'IGD']);
        DB::table('mobil_cinta_riwayat_aktivitas')->insert([
            'perjalanan_id' => $journey->id, 'dilakukan_oleh_id' => $account->id, 'jenis_aktivitas' => 'legacy',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $migration = require base_path('database/migrations/2026_10_29_000002_cleanup_legacy_mobil_cinta_tables.php');
        try {
            $migration->up();
            $this->fail('Cleanup should stop while the activity table has rows.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('berisi 1 baris', $exception->getMessage());
        }
        $this->assertTrue(Schema::hasTable('mobil_cinta_accounts'));
        $this->assertTrue(Schema::hasTable('mobil_cinta_riwayat_aktivitas'));
    }

    public function test_coordinate_migration_keeps_data_and_uses_final_column_names(): void
    {
        $this->assertTrue(Schema::hasColumn('mobil_cinta_perjalanan', 'latitude_berangkat'));
        $this->assertTrue(Schema::hasColumn('mobil_cinta_perjalanan', 'longitude_berangkat'));
        $this->assertTrue(Schema::hasColumn('mobil_cinta_perjalanan', 'latitude_sampai'));
        $this->assertTrue(Schema::hasColumn('mobil_cinta_perjalanan', 'longitude_sampai'));
        $this->assertTrue(Schema::hasColumn('mobil_cinta_perjalanan', 'nama_sopir'));
        $this->assertFalse(Schema::hasColumn('mobil_cinta_perjalanan', 'lintang_berangkat'));
    }

    public function test_cleanup_stops_when_legacy_and_final_counts_do_not_match(): void
    {
        DB::table('mobil_cinta_roles')->insert(['name' => 'admin', 'created_at' => now(), 'updated_at' => now()]);
        $migration = require base_path('database/migrations/2026_10_29_000002_cleanup_legacy_mobil_cinta_tables.php');
        try {
            $migration->up();
            $this->fail('Cleanup should stop when source and target records differ.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('jumlah baris mobil_cinta_roles', $exception->getMessage());
        }
        $this->assertTrue(Schema::hasTable('mobil_cinta_roles'));
        $this->assertTrue(Schema::hasTable('mobil_cinta_peran'));
    }
}
