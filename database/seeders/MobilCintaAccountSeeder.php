<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class MobilCintaAccountSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('mobil_cinta_peran')
            ->pluck('id', 'nama_peran');

        foreach (['admin', 'koordinator', 'perawat', 'sopir'] as $role) {
            if (!$roles->has($role)) {
                throw new RuntimeException(
                    "Role {$role} belum tersedia. Jalankan MobilCintaRoleSeeder terlebih dahulu."
                );
            }
        }

        DB::transaction(function () use ($roles) {

            $mainAccounts = [
                [
                    'nama' => 'Admin Mobil Cinta',
                    'username' => 'admin',
                    'role' => 'admin',
                    'password' => '4dminrsdkalisat',
                ],
                [
                    'nama' => 'Perawat Mobil Cinta',
                    'username' => 'perawat',
                    'role' => 'perawat',
                    'password' => '4dminperawat',
                ],
                [
                    'nama' => 'Koordinator Mobil Cinta',
                    'username' => 'koordinator',
                    'role' => 'koordinator',
                    'password' => '4dminkoordinator',
                ],
            ];

            $createdMain = 0;
            $updatedMain = 0;

            foreach ($mainAccounts as $account) {

                $roleId = $roles[$account['role']];

                $existing = DB::table('mobil_cinta_akun')
                    ->where('nama_pengguna', $account['username'])
                    ->first();

                if ($existing) {

                    // Jangan mengubah akun jika username digunakan role lain.
                    if ((int) $existing->peran_id !== (int) $roleId) {
                        throw new RuntimeException(
                            "Username {$account['username']} sudah digunakan oleh akun dengan role berbeda."
                        );
                    }

                    DB::table('mobil_cinta_akun')
                        ->where('id', $existing->id)
                        ->update([
                            'nama' => $account['nama'],
                            'kata_sandi' => Hash::make($account['password']),
                            'status_aktif' => true,
                            'updated_at' => now(),
                        ]);

                    $updatedMain++;

                } else {

                    DB::table('mobil_cinta_akun')->insert([
                        'peran_id' => $roleId,
                        'nama' => $account['nama'],
                        'nama_pengguna' => $account['username'],
                        'kata_sandi' => Hash::make($account['password']),
                        'status_aktif' => true,
                        'login_terakhir_pada' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $createdMain++;
                }
            }

            $drivers = [
                'DODIK KURNIAWAN',
                'DIMAS AIRLANGGA',
                'MOH AHLUS SOLEHAN',
                'MUH SAMSUL HARIFIN',
                'AHMAD ZAENURI',
                'VIRGIAWAN SHANDY YUDHA',
                'MUHAMMAD ERFAN EFENDI',
                'ANGGA ROSANU KRESDIANTO',
            ];

            $createdDrivers = 0;
            $skippedDrivers = 0;

            foreach ($drivers as $name) {

                $existing = DB::table('mobil_cinta_akun')
                    ->where('nama_pengguna', $name)
                    ->first();

                if ($existing) {

                    if ((int) $existing->peran_id !== (int) $roles['sopir']) {
                        throw new RuntimeException(
                            "Username {$name} sudah digunakan oleh akun dengan role berbeda."
                        );
                    }

                    // Tidak menimpa akun Sopir yang sudah tersedia.
                    $skippedDrivers++;

                    continue;
                }

                DB::table('mobil_cinta_akun')->insert([
                    'peran_id' => $roles['sopir'],
                    'nama' => $name,
                    'nama_pengguna' => $name,
                    'kata_sandi' => Hash::make('4dminsopir'),
                    'status_aktif' => true,
                    'login_terakhir_pada' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $createdDrivers++;
            }

            /*
            |--------------------------------------------------------------------------
            | 3. INFORMASI HASIL SEEDER
            |--------------------------------------------------------------------------
            */

            $this->command?->info('======================================');
            $this->command?->info('SEEDER AKUN MOBIL CINTA SELESAI');
            $this->command?->info('======================================');

            $this->command?->info(
                "Akun utama baru dibuat : {$createdMain}"
            );

            $this->command?->info(
                "Akun utama diperbarui  : {$updatedMain}"
            );

            $this->command?->info(
                "Akun Sopir baru dibuat : {$createdDrivers}"
            );

            $this->command?->info(
                "Akun Sopir dilewati    : {$skippedDrivers}"
            );
        });
    }
}