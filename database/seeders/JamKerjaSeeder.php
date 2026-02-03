<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JamKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lokasi')->insert([
            [
                'nama_lokasi' => 'RSDKalisat',
                'alamat' => 'Jl. Raya Utama No. 1',
                'latitude' => -8.219233,
                'longitude' => 114.369141,
                'radius_meter' => 50,
            ],
            [
                'nama_lokasi' => 'Gedung Pelayanan',
                'alamat' => 'Jl. Pelayanan No. 10',
                'latitude' => -8.220100,
                'longitude' => 114.370200,
                'radius_meter' => 30,
            ],
        ]);
    }
}
