<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('jam_kerja')->insert([
            [
                'nama_jam_kerja' => 'Shift Pagi',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '15:00:00',
                'toleransi_menit' => 10,
            ],
            [
                'nama_jam_kerja' => 'Shift Siang',
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '21:00:00',
                'toleransi_menit' => 10,
            ],
            [
                'nama_jam_kerja' => 'Shift Malam',
                'jam_mulai' => '21:00:00',
                'jam_selesai' => '07:00:00',
                'toleransi_menit' => 15,
            ],
        ]);
    }
}
