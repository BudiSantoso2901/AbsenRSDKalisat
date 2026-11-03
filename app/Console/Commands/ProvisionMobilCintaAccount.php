<?php

namespace App\Console\Commands;

use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ProvisionMobilCintaAccount extends Command
{
    protected $signature = 'mobil-cinta:account:create';

    protected $description = 'Create a Mobil Cinta account through a private interactive prompt';

    public function handle(): int
    {
        if (!$this->input->isInteractive()) {
            $this->error('Perintah ini harus dijalankan secara interaktif agar password tidak ditulis di argumen atau log.');

            return self::FAILURE;
        }

        $roles = Role::query()->whereIn('nama_peran', ['admin', 'koordinator', 'perawat', 'sopir'])->orderBy('nama_peran')->pluck('nama_peran')->all();

        if ($roles === []) {
            $this->error('Role Mobil Cinta belum tersedia. Jalankan seeder MobilCintaRoleSeeder terlebih dahulu.');

            return self::FAILURE;
        }

        $name = trim((string) $this->ask('Nama akun'));
        $username = trim((string) $this->ask('Username'));
        $roleName = $this->choice('Role', $roles);

        if ($name === '' || mb_strlen($name) > 255 || $username === '' || mb_strlen($username) > 255) {
            $this->error('Nama dan username wajib diisi, maksimal 255 karakter.');

            return self::FAILURE;
        }

        if (Account::query()->where('nama_pengguna', $username)->exists()) {
            $this->error('Username sudah digunakan.');

            return self::FAILURE;
        }

        $password = (string) $this->secret('Password baru (minimal 14 karakter)');
        $confirmation = (string) $this->secret('Ulangi password');

        if (mb_strlen($password) < 14 || !hash_equals($password, $confirmation)) {
            $this->error('Password harus minimal 14 karakter dan kedua isian harus sama.');

            return self::FAILURE;
        }

        $account = new Account();
        $account->peran_id = Role::query()->where('nama_peran', $roleName)->value('id');
        $account->nama = $name;
        $account->nama_pengguna = $username;
        $account->kata_sandi = Hash::make($password);
        $account->status_aktif = true;
        $account->save();

        $this->info("Akun Mobil Cinta '{$username}' dengan role '{$roleName}' berhasil dibuat.");

        return self::SUCCESS;
    }
}
