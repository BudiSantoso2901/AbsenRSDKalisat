<?php

namespace Tests\Feature\MobilCinta;

use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Role;
use App\Models\Pegawai;
use App\Models\User;
use Database\Seeders\MobilCintaRoleSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\MobilCinta\MobilCintaTestCase;

class MobilCintaAuthenticationTest extends MobilCintaTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        (new MobilCintaRoleSeeder())->run();
    }

    public function test_valid_account_can_log_in_through_the_indonesian_password_column(): void
    {
        $account = $this->createAccount('admin', 'admin.test');

        $this->post(route('mobil-cinta.login.submit'), [
            'username' => 'admin.test',
            'password' => 'A-unique-Test-Password-9021',
        ])->assertRedirect(route('mobil-cinta.admin.dashboard'));

        $this->assertAuthenticatedAs($account, 'mobil_cinta');
        $this->assertNotNull($account->fresh()->login_terakhir_pada);
        $this->assertTrue(Hash::check('A-unique-Test-Password-9021', $account->fresh()->kata_sandi));
        $this->assertArrayNotHasKey('kata_sandi', $account->fresh()->toArray());
        $this->get(route('login', ['akses' => 'absensi']))->assertOk()->assertSee('Username / NIP');
    }

    public function test_wrong_password_and_inactive_accounts_cannot_log_in(): void
    {
        $this->createAccount('admin', 'admin.wrong-password');
        $this->createAccount('perawat', 'perawat.inactive', false);

        $this->post(route('mobil-cinta.login.submit'), [
            'username' => 'admin.wrong-password',
            'password' => 'incorrect-password',
        ])->assertRedirect(route('mobil-cinta.login'))
            ->assertSessionHasErrors('username');

        $this->post(route('mobil-cinta.login.submit'), [
            'username' => 'perawat.inactive',
            'password' => 'A-unique-Test-Password-9021',
        ])->assertSessionHasErrors('username');

        $this->assertGuest('mobil_cinta');
    }

    public function test_mobil_cinta_has_a_separate_login_page(): void
    {
        $this->get(route('mobil-cinta.login'))
            ->assertOk()
            ->assertSee('Mobil Cinta')
            ->assertSee('name="username"', false)
            ->assertSee(route('mobil-cinta.login.submit'), false);
    }

    public function test_mobil_cinta_login_and_logout_preserve_an_active_absensi_guard(): void
    {
        $account = $this->createAccount('admin', 'admin.session-preserve');
        $pegawai = $this->createPegawai();
        $this->actingAs($pegawai, 'pegawai');

        $this->get(route('mobil-cinta.login'))
            ->assertOk()
            ->assertSee('name="username"', false);

        $this->post(route('mobil-cinta.login.submit'), [
            'username' => 'admin.session-preserve',
            'password' => 'A-unique-Test-Password-9021',
        ])->assertRedirect(route('mobil-cinta.admin.dashboard'));
        $this->assertAuthenticatedAs($pegawai, 'pegawai');
        $this->assertAuthenticatedAs($account, 'mobil_cinta');

        $this->post(route('mobil-cinta.logout'))->assertRedirect(route('mobil-cinta.login'));
        $this->assertAuthenticatedAs($pegawai, 'pegawai');
        $this->assertGuest('mobil_cinta');
    }

    public function test_absensi_login_ends_a_previously_active_mobil_cinta_session(): void
    {
        $account = $this->createAccount('admin', 'admin.session-replaced');
        $this->post(route('mobil-cinta.login.submit'), [
            'username' => 'admin.session-replaced',
            'password' => 'A-unique-Test-Password-9021',
        ])->assertRedirect(route('mobil-cinta.admin.dashboard'));
        $this->assertAuthenticatedAs($account, 'mobil_cinta');

        DB::table('users')->insert([
            'name' => 'abs-admin-test',
            'email' => 'abs-admin-test@example.test',
            'password' => Hash::make('legacy-test-password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post(route('login.process'), [
            'login' => 'abs-admin-test',
            'password' => 'legacy-test-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated('web');
        $this->assertGuest('mobil_cinta');
    }

    public function test_failed_absensi_login_also_invalidates_mobil_cinta_and_absensi_logout_clears_both_guards(): void
    {
        $this->createAccount('admin', 'admin.failed-absensi');
        $this->post(route('mobil-cinta.login.submit'), [
            'username' => 'admin.failed-absensi',
            'password' => 'A-unique-Test-Password-9021',
        ]);
        $this->assertAuthenticated('mobil_cinta');

        $this->post(route('login.process'), ['login' => 'not-a-user', 'password' => 'wrong'])
            ->assertRedirect(route('login'));
        $this->assertGuest('mobil_cinta');

        $pegawai = $this->createPegawai();
        $this->actingAs($pegawai, 'pegawai');
        $this->createAccount('perawat', 'perawat.absensi-logout');
        $this->post(route('mobil-cinta.login.submit'), [
            'username' => 'perawat.absensi-logout',
            'password' => 'A-unique-Test-Password-9021',
        ]);
        $this->assertAuthenticated('mobil_cinta');
        $this->assertAuthenticated('pegawai');

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest('pegawai');
        $this->assertGuest('mobil_cinta');
    }

    private function createAccount(string $roleName, string $username, bool $active = true): Account
    {
        return Account::query()->create([
            'peran_id' => Role::query()->where('nama_peran', $roleName)->value('id'),
            'nama' => ucfirst($roleName).' Test',
            'nama_pengguna' => $username,
            'kata_sandi' => 'A-unique-Test-Password-9021',
            'status_aktif' => $active,
        ]);
    }

    private function createPegawai(): Pegawai
    {
        $id = DB::table('pegawai')->insertGetId([
            'name' => 'Pegawai Absensi Test',
            'nip' => 'TEST-PEG-001',
            'tanggal_lahir' => '1990-01-01',
            'email' => 'pegawai-test@example.test',
            'password' => Hash::make('legacy-test-password'),
            'id_jabatan' => 1,
            'id_lokasi' => 1,
            'id_jam_kerja' => 1,
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Pegawai::query()->findOrFail($id);
    }
}
