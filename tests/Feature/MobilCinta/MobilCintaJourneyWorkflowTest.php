<?php

namespace Tests\Feature\MobilCinta;

use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Journey;
use App\Models\MobilCinta\Role;
use App\Models\MobilCinta\Vehicle;
use App\Models\Ruangan;
use App\Services\MobilCinta\JourneyListQueryService;
use App\Services\MobilCinta\JourneyWorkflowService;
use Database\Seeders\MobilCintaRoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\Feature\MobilCinta\MobilCintaTestCase;

class MobilCintaJourneyWorkflowTest extends MobilCintaTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        (new MobilCintaRoleSeeder())->run();
    }

    public function test_nurse_request_requires_every_visible_business_field_and_server_owns_status(): void
    {
        $nurse = $this->account('perawat');
        $workflow = app(JourneyWorkflowService::class);
        try {
            $workflow->createRequest($nurse, ['nama_pasien' => 'Pasien test']);
            $this->fail('Incomplete nurse request should fail validation.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('ruangan_id', $exception->errors());
        }

        $room = Ruangan::query()->create(['nama_ruangan' => 'Ruangan Uji']);
        $journey = $workflow->createRequest($nurse, [
            'ruangan_id' => $room->id, 'nama_pasien' => 'Pasien test', 'nomor_rekam_medis' => 'RM-TEST',
            'jenis_kelamin_pasien' => 'P', 'penjamin' => 'Umum', 'tujuan' => 'IGD', 'catatan' => 'Perlu pendampingan',
            'status' => Journey::STATUS_COMPLETED, 'diajukan_oleh_id' => 999,
        ]);
        $this->assertSame(Journey::STATUS_REQUESTED, $journey->status);
        $this->assertSame($nurse->id, $journey->diajukan_oleh_id);
        $this->assertNotEmpty($journey->nomor_perjalanan);
        $this->assertNotNull($journey->diajukan_pada);
    }

    public function test_departure_requires_gps_photo_and_odometer_then_snapshots_assigned_driver_name(): void
    {
        Storage::fake('mobil_cinta_private');
        [$journey, $driver] = $this->assignedJourney();
        $workflow = app(JourneyWorkflowService::class);
        try {
            $workflow->depart($journey, $driver, ['kilometer_berangkat' => 10]);
            $this->fail('Incomplete departure should fail validation.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('latitude_berangkat', $exception->errors());
            $this->assertSame(Journey::STATUS_ASSIGNED, $journey->fresh()->status);
        }
        $departed = $workflow->depart($journey, $driver, [
            'kilometer_berangkat' => 10, 'latitude_berangkat' => -8.1, 'longitude_berangkat' => 113.2,
            'foto_berangkat' => UploadedFile::fake()->image('start.jpg'), 'nama_sopir' => 'Dipalsukan',
        ]);
        $this->assertSame(Journey::STATUS_DEPARTED, $departed->status);
        $this->assertSame($driver->nama, $departed->nama_sopir);
        $this->assertNotNull($departed->waktu_berangkat);
        Storage::disk('mobil_cinta_private')->assertExists($departed->foto_berangkat);
    }

    public function test_arrival_requires_complete_data_and_non_decreasing_odometer(): void
    {
        Storage::fake('mobil_cinta_private');
        [$journey, $driver] = $this->assignedJourney();
        $workflow = app(JourneyWorkflowService::class);
        $journey = $workflow->depart($journey, $driver, [
            'kilometer_berangkat' => 10, 'latitude_berangkat' => -8.1, 'longitude_berangkat' => 113.2,
            'foto_berangkat' => UploadedFile::fake()->image('start.jpg'),
        ]);
        try {
            $workflow->arrive($journey, $driver, [
                'kilometer_sampai' => 9, 'latitude_sampai' => -8.2, 'longitude_sampai' => 113.3,
                'foto_sampai' => UploadedFile::fake()->image('end.jpg'),
            ]);
            $this->fail('Odometer below the departure value must be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('kilometer_sampai', $exception->errors());
        }
        $journey = $workflow->arrive($journey, $driver, [
            'kilometer_sampai' => 12, 'latitude_sampai' => -8.2, 'longitude_sampai' => 113.3,
            'foto_sampai' => UploadedFile::fake()->image('end.jpg'),
        ]);
        $this->assertSame(Journey::STATUS_COMPLETED, $journey->status);
        $this->assertNotNull($journey->waktu_sampai);
    }

    public function test_driver_history_is_scoped_to_current_assignment_and_journey_tables_are_final(): void
    {
        [$assigned, $driver] = $this->assignedJourney();
        $otherDriver = $this->account('sopir');
        $other = Journey::query()->create([
            'nomor_perjalanan' => 'MC-OTHER', 'diajukan_oleh_id' => $this->account('perawat')->id,
            'nama_pasien' => 'Pasien lain', 'nomor_rekam_medis' => 'RM-OTHER', 'penjamin' => 'Umum', 'tujuan' => 'IGD',
            'sopir_id' => $otherDriver->id, 'nama_sopir' => $otherDriver->nama,
        ]);
        $this->assertSame([$assigned->id], app(JourneyListQueryService::class)->forAccount($driver)->pluck('id')->all());
        $this->assertSame('mobil_cinta_perjalanan', (new Journey())->getTable());
        $this->assertSame('mobil_cinta_akun', (new Account())->getTable());
        $this->assertNotSame('secret-test', $driver->getAuthPassword());
        $this->assertDatabaseMissing('mobil_cinta_perjalanan', ['id' => $other->id, 'sopir_id' => $driver->id]);
    }

    public function test_role_middleware_blocks_other_dashboards_and_operational_pages_render(): void
    {
        [$journey, $driver] = $this->assignedJourney();
        $this->actingAs($driver, 'mobil_cinta');
        $this->get(route('mobil-cinta.perawat.dashboard'))->assertForbidden();
        $this->get(route('mobil-cinta.sopir.dashboard'))->assertOk()->assertSee($journey->nomor_perjalanan);
        $this->get(route('mobil-cinta.sopir.riwayat'))->assertOk()->assertSee($journey->nomor_perjalanan);
    }

    private function assignedJourney(): array
    {
        $nurse = $this->account('perawat');
        $coordinator = $this->account('koordinator');
        $driver = $this->account('sopir');
        $room = Ruangan::query()->create(['nama_ruangan' => 'Ruangan Uji']);
        $vehicle = Vehicle::query()->create(['nomor_polisi' => 'N 1001 AA']);
        $workflow = app(JourneyWorkflowService::class);
        $journey = $workflow->createRequest($nurse, [
            'ruangan_id' => $room->id, 'nama_pasien' => 'Pasien test', 'nomor_rekam_medis' => 'RM-TEST',
            'jenis_kelamin_pasien' => 'P', 'penjamin' => 'Umum', 'tujuan' => 'IGD', 'catatan' => 'Catatan uji',
        ]);
        $journey = $workflow->assign($journey, $driver, $vehicle, $coordinator);
        return [$journey, $driver];
    }

    private function account(string $role): Account
    {
        static $index = 0;
        $index++;
        return Account::query()->create([
            'peran_id' => Role::query()->where('nama_peran', $role)->value('id'), 'nama' => ucfirst($role).' User '.$index,
            'nama_pengguna' => $role.'.test.'.$index, 'kata_sandi' => 'secret-test', 'status_aktif' => true,
        ]);
    }
}
