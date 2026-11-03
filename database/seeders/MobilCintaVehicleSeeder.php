<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MobilCintaVehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            'P 1381 GP',
            'P 1423 GP',
            'P 1369 GP',
            'P 1394 GP',
            'P 1347 GP',
            'P 1421 GP',
            'P 1434 GP',
            'P 1954 HP',
        ];

        $created = 0;
        $skipped = 0;

        foreach ($vehicles as $plateNumber) {
            $exists = DB::table('mobil_cinta_kendaraan')
                ->where('nomor_polisi', $plateNumber)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            DB::table('mobil_cinta_kendaraan')->insert([
                'nomor_polisi' => $plateNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $created++;
        }

        $this->command?->info("Kendaraan baru dibuat: {$created}");
        $this->command?->info("Kendaraan sudah tersedia: {$skipped}");
    }
}