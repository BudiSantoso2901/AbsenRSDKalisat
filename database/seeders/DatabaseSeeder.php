<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */ public function run(): void
    {
        $this->call([
            JabatanSeeder::class,
            LokasiSeeder::class,
            JamKerjaSeeder::class,
            // PegawaiSeeder::class,
            UserSeeder::class,
        ]);
    }
}
