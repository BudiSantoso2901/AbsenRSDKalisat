<?php

namespace Tests\Feature\MobilCinta;

use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Journey;
use App\Models\MobilCinta\Role;
use App\Models\MobilCinta\Vehicle;
use App\Models\Ruangan;
use Database\Seeders\MobilCintaRoleSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Feature\MobilCinta\MobilCintaTestCase;

class MobilCintaSchemaTest extends MobilCintaTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        (new MobilCintaRoleSeeder())->run();
    }

    public function test_role_seeder_is_idempotent_and_legacy_absensi_schema_is_unchanged(): void
    {
        (new MobilCintaRoleSeeder())->run();

        $this->assertDatabaseCount('mobil_cinta_peran', 4);
        $this->assertSame($this->legacyColumnsBeforeModuleMigration(), $this->legacyColumns());
        $this->assertSame(['id', 'nama_ruangan', 'created_at', 'updated_at'], Schema::getColumnListing('ruangans'));
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('pegawai'));
        $this->assertTrue(Schema::hasTable('absensi'));
        $this->assertTrue(Schema::hasTable('absenkontens'));
    }

    public function test_final_schema_has_expected_foreign_keys_unique_keys_indexes_and_nullable_fields(): void
    {
        $foreignKeys = [];
        foreach (['mobil_cinta_akun', 'mobil_cinta_perjalanan'] as $table) {
            foreach (Schema::getForeignKeys($table) as $foreignKey) {
                $foreignKeys[$table.'.'.$foreignKey['columns'][0]] = $foreignKey['foreign_table'].'.'.$foreignKey['foreign_columns'][0];
            }
        }
        ksort($foreignKeys);

        $expected = [
            'mobil_cinta_akun.peran_id' => 'mobil_cinta_peran.id',
            'mobil_cinta_perjalanan.diajukan_oleh_id' => 'mobil_cinta_akun.id',
            'mobil_cinta_perjalanan.ruangan_id' => 'ruangans.id',
            'mobil_cinta_perjalanan.sopir_id' => 'mobil_cinta_akun.id',
            'mobil_cinta_perjalanan.kendaraan_id' => 'mobil_cinta_kendaraan.id',
            'mobil_cinta_perjalanan.ditugaskan_oleh_id' => 'mobil_cinta_akun.id',
        ];
        ksort($expected);
        $this->assertSame($expected, $foreignKeys);

        foreach ([
            ['mobil_cinta_peran', ['nama_peran'], 'unique'],
            ['mobil_cinta_akun', ['nama_pengguna'], 'unique'],
            ['mobil_cinta_kendaraan', ['nomor_polisi'], 'unique'],
            ['mobil_cinta_perjalanan', ['nomor_perjalanan'], 'unique'],
            ['mobil_cinta_akun', ['peran_id'], null],
            ['mobil_cinta_perjalanan', ['diajukan_oleh_id'], null],
            ['mobil_cinta_perjalanan', ['sopir_id'], null],
            ['mobil_cinta_perjalanan', ['kendaraan_id'], null],
            ['mobil_cinta_perjalanan', ['ditugaskan_oleh_id'], null],
            ['mobil_cinta_perjalanan', ['status', 'diajukan_pada'], null],
        ] as [$table, $columns, $type]) {
            $this->assertTrue(Schema::hasIndex($table, $columns, $type), $table.' index '.implode(',', $columns));
        }

        foreach ([
            'mobil_cinta_akun.login_terakhir_pada',
            'mobil_cinta_perjalanan.ruangan_id',
            'mobil_cinta_perjalanan.jenis_kelamin_pasien',
            'mobil_cinta_perjalanan.catatan',
            'mobil_cinta_perjalanan.diajukan_pada',
            'mobil_cinta_perjalanan.sopir_id',
            'mobil_cinta_perjalanan.kendaraan_id',
            'mobil_cinta_perjalanan.ditugaskan_oleh_id',
            'mobil_cinta_perjalanan.ditugaskan_pada',
            'mobil_cinta_perjalanan.kilometer_berangkat',
            'mobil_cinta_perjalanan.latitude_berangkat',
            'mobil_cinta_perjalanan.longitude_berangkat',
            'mobil_cinta_perjalanan.foto_berangkat',
            'mobil_cinta_perjalanan.waktu_berangkat',
            'mobil_cinta_perjalanan.kilometer_sampai',
            'mobil_cinta_perjalanan.latitude_sampai',
            'mobil_cinta_perjalanan.longitude_sampai',
            'mobil_cinta_perjalanan.foto_sampai',
            'mobil_cinta_perjalanan.waktu_sampai',
            'mobil_cinta_perjalanan.catatan_sopir',
            'mobil_cinta_perjalanan.nama_sopir',
        ] as $qualifiedColumn) {
            [$table, $column] = explode('.', $qualifiedColumn, 2);
            $definition = collect(Schema::getColumns($table))->firstWhere('name', $column);
            $this->assertNotNull($definition, 'Missing '.$qualifiedColumn);
            $this->assertTrue($definition['nullable'], $qualifiedColumn.' must be nullable');
        }

        $this->assertSame(['id', 'nomor_polisi', 'created_at', 'updated_at'], Schema::getColumnListing('mobil_cinta_kendaraan'));
        $this->assertSame([
            'id', 'nama_peran', 'created_at', 'updated_at',
        ], Schema::getColumnListing('mobil_cinta_peran'));
    }

    public function test_models_use_the_final_indonesian_tables_and_password_hash_is_hidden(): void
    {
        $this->assertSame(Account::class, config('auth.providers.mobil_cinta_accounts.model'));
        $account = $this->account('perawat', 'nurse.schema');
        $room = Ruangan::query()->create(['nama_ruangan' => 'IGD']);
        $vehicle = Vehicle::query()->create(['nomor_polisi' => 'N 1001 AA']);
        $journey = Journey::query()->create([
            'nomor_perjalanan' => 'MC-SCHEMA-001',
            'diajukan_oleh_id' => $account->getKey(),
            'ruangan_id' => $room->getKey(),
            'nama_pasien' => 'Pasien Uji',
            'nomor_rekam_medis' => 'RM-TEST-01',
            'penjamin' => 'BPJS',
            'tujuan' => 'Rumah Sakit Rujukan',
            'kendaraan_id' => $vehicle->getKey(),
            'status' => Journey::STATUS_COMPLETED,
        ]);

        $this->assertSame(Journey::STATUS_REQUESTED, $journey->status, 'Mass-assigned status must not bypass the server workflow.');
        $this->assertSame($account->getKey(), $journey->requester->getKey());
        $this->assertSame($room->getKey(), $journey->room->getKey());
        $this->assertSame($vehicle->getKey(), $journey->vehicle->getKey());
        $this->assertDatabaseHas('mobil_cinta_perjalanan', ['id' => $journey->getKey(), 'status' => Journey::STATUS_REQUESTED]);
        $this->assertArrayNotHasKey('kata_sandi', $account->fresh()->toArray());
        $this->assertNotSame('A-unique-Test-Password-9021', $account->fresh()->getAuthPassword());
    }

    public function test_journey_photos_use_a_private_disk_outside_the_public_storage_root(): void
    {
        $disk = config('filesystems.disks.mobil_cinta_private');
        $this->assertSame('local', $disk['driver']);
        $this->assertSame('private', $disk['visibility']);
        $this->assertStringNotContainsString(DIRECTORY_SEPARATOR.'public', $disk['root']);
        $this->assertStringContainsString('mobil-cinta-private', $disk['root']);
    }

    public function test_foreign_keys_and_unique_constraints_are_enforced(): void
    {
        $this->account('admin', 'admin.unique');

        try {
            DB::table('mobil_cinta_akun')->insert([
                'peran_id' => 99999,
                'nama' => 'Invalid',
                'nama_pengguna' => 'invalid.fk',
                'kata_sandi' => bcrypt('test-only-password'),
                'status_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->fail('Invalid peran_id should violate the foreign key.');
        } catch (QueryException) {
            $this->assertDatabaseMissing('mobil_cinta_akun', ['nama_pengguna' => 'invalid.fk']);
        }

        $this->expectException(QueryException::class);
        DB::table('mobil_cinta_peran')->insert(['nama_peran' => 'admin', 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_erd_documentation_names_the_implemented_tables_and_key_columns(): void
    {
        $markdown = file_get_contents(base_path('docs/mobil-cinta-erd.md'));
        $svg = file_get_contents(base_path('docs/mobil-cinta-erd.svg'));

        foreach ([
            'mobil_cinta_peran', 'mobil_cinta_akun', 'mobil_cinta_kendaraan',
            'mobil_cinta_perjalanan', 'ruangans',
            'kata_sandi', 'nomor_polisi', 'nomor_perjalanan', 'latitude_berangkat', 'longitude_berangkat',
            'latitude_sampai', 'longitude_sampai', 'nama_sopir',
        ] as $schemaName) {
            $this->assertStringContainsString($schemaName, $markdown);
            $this->assertStringContainsString($schemaName, $svg);
        }

        $this->assertStringContainsString('tidak diubah', mb_strtolower($markdown));
        $this->assertStringContainsString('tidak diubah', mb_strtolower($svg));
    }

    private function account(string $roleName, string $username): Account
    {
        return Account::query()->create([
            'peran_id' => Role::query()->where('nama_peran', $roleName)->value('id'),
            'nama' => ucfirst($roleName).' Test',
            'nama_pengguna' => $username,
            'kata_sandi' => 'A-unique-Test-Password-9021',
            'status_aktif' => true,
        ]);
    }
}
