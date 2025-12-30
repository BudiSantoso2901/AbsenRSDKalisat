<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jabatan')->insert([
            ['nama_jabatan' => 'Admin'],
            ['nama_jabatan' => 'Kepala Bagian'],
            ['nama_jabatan' => 'Staff'],
            ['nama_jabatan' => 'Petugas Lapangan'],
        ]);
    }
}
