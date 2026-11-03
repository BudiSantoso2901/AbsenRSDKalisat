<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MobilCintaRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin',
            'koordinator',
            'perawat',
            'sopir',
        ];

        foreach ($roles as $role) {
            $exists = DB::table('mobil_cinta_peran')
                ->where('nama_peran', $role)
                ->exists();

            if (!$exists) {
                DB::table('mobil_cinta_peran')->insert([
                    'nama_peran' => $role,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command?->info('Master role Mobil Cinta berhasil diperiksa.');
    }
}