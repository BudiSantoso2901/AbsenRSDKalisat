<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MobilCintaMasterSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MobilCintaRoleSeeder::class,
            MobilCintaAccountSeeder::class,
            MobilCintaVehicleSeeder::class,
        ]);
    }
}