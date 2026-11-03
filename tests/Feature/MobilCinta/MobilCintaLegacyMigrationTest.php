<?php

namespace Tests\Feature\MobilCinta;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MobilCintaLegacyMigrationTest extends MobilCintaTestCase
{
    protected bool $runFinalSchemaMigration = false;

    public function test_coordinate_rename_preserves_copied_journey_values_and_snapshots_driver_name(): void
    {
        $now = now()->toDateTimeString();
        $nurseRole = DB::table('mobil_cinta_roles')->insertGetId(['name' => 'nurse', 'created_at' => $now, 'updated_at' => $now]);
        $driverRole = DB::table('mobil_cinta_roles')->insertGetId(['name' => 'driver', 'created_at' => $now, 'updated_at' => $now]);
        $coordinatorRole = DB::table('mobil_cinta_roles')->insertGetId(['name' => 'coordinator', 'created_at' => $now, 'updated_at' => $now]);
        $nurseId = $this->legacyAccount($nurseRole, 'nurse.coordinate.test', 'Perawat Uji');
        $driverId = $this->legacyAccount($driverRole, 'driver.coordinate.test', 'Sopir Snapshot');
        $coordinatorId = $this->legacyAccount($coordinatorRole, 'coordinator.coordinate.test', 'Koordinator Uji');
        $vehicleId = DB::table('mobil_cinta_vehicles')->insertGetId(['plate_number' => 'N 8001 AA', 'vehicle_name' => 'legacy', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
        $requestId = DB::table('mobil_cinta_trip_requests')->insertGetId([
            'request_number' => 'MC-COORDINATE', 'requested_by' => $nurseId, 'patient_name' => 'Pasien uji', 'medical_record_no' => 'RM-COORDINATE',
            'destination' => 'IGD', 'status' => 'completed', 'submitted_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $assignmentId = DB::table('mobil_cinta_assignments')->insertGetId([
            'trip_request_id' => $requestId, 'driver_id' => $driverId, 'vehicle_id' => $vehicleId, 'assigned_by' => $coordinatorId,
            'assignment_status' => 'active', 'assigned_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('mobil_cinta_journeys')->insert([
            'assignment_id' => $assignmentId, 'start_km' => 120.5, 'end_km' => 125.5,
            'start_latitude' => -8.1234567, 'start_longitude' => 113.1234567, 'end_latitude' => -8.2234567, 'end_longitude' => 113.2234567,
            'started_at' => $now, 'finished_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $this->runFinalMigration();
        $this->runSchemaAdjustmentMigration();
        $journey = DB::table('mobil_cinta_perjalanan')->where('id', $requestId)->first();
        $this->assertSame('Sopir Snapshot', $journey->nama_sopir);
        $this->assertEqualsWithDelta(-8.1234567, (float) $journey->latitude_berangkat, 0.0000001);
        $this->assertEqualsWithDelta(113.1234567, (float) $journey->longitude_berangkat, 0.0000001);
        $this->assertEqualsWithDelta(-8.2234567, (float) $journey->latitude_sampai, 0.0000001);
        $this->assertEqualsWithDelta(113.2234567, (float) $journey->longitude_sampai, 0.0000001);
    }

    public function test_final_copy_preserves_account_hash_and_cleanup_removes_only_legacy_tables(): void
    {
        $now = now()->toDateTimeString();
        $roleId = DB::table('mobil_cinta_roles')->insertGetId(['name' => 'administrator', 'created_at' => $now, 'updated_at' => $now]);
        $hash = Hash::make('test-only-secret');
        $accountId = DB::table('mobil_cinta_accounts')->insertGetId([
            'role_id' => $roleId, 'name' => 'Admin test', 'username' => 'admin.migration.test', 'password' => $hash,
            'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $vehicleId = DB::table('mobil_cinta_vehicles')->insertGetId([
            'plate_number' => 'N 7001 AA', 'vehicle_name' => 'legacy', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $this->runFinalMigration();
        $this->runSchemaAdjustmentMigration();
        $this->assertSame($hash, DB::table('mobil_cinta_akun')->where('id', $accountId)->value('kata_sandi'));
        $this->assertSame('admin', DB::table('mobil_cinta_peran')->where('id', $roleId)->value('nama_peran'));
        $this->assertSame('N 7001 AA', DB::table('mobil_cinta_kendaraan')->where('id', $vehicleId)->value('nomor_polisi'));

        $this->runCleanupMigration();
        $this->assertTrue(Schema::hasTable('mobil_cinta_akun'));
        $this->assertFalse(Schema::hasTable('mobil_cinta_accounts'));
        $this->assertTrue(Schema::hasTable('ruangans'));
        $this->assertTrue(Schema::hasTable('absensi'));
    }

    private function legacyAccount(int $roleId, string $username, string $name): int
    {
        return DB::table('mobil_cinta_accounts')->insertGetId([
            'role_id' => $roleId, 'name' => $name, 'username' => $username, 'password' => Hash::make('test-only-secret'),
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
