<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Pegawai;
use Carbon\Carbon;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = [
            [
                'name' => 'Ahmad Fauzi',
                'nip' => '202401010000000001',
                'tanggal_lahir' => '1998-01-29',
                'email' => 'ahmad@rsd.com',
                'id_jabatan' => 1,
                'id_lokasi' => 1,
                'id_jam_kerja' => 1,
                'status' => 'approved',
            ],
            [
                'name' => 'Siti Aisyah',
                'nip' => '202401010000000002',
                'tanggal_lahir' => '1999-03-12',
                'email' => 'aisyah@rsd.com',
                'id_jabatan' => 2,
                'id_lokasi' => 1,
                'id_jam_kerja' => 1,
                'status' => 'approved',
            ],
            [
                'name' => 'Budi Santoso',
                'nip' => '202401010000000003',
                'tanggal_lahir' => '1997-07-21',
                'email' => 'budi@rsd.com',
                'id_jabatan' => 1,
                'id_lokasi' => 2,
                'id_jam_kerja' => 2,
                'status' => 'pending',
            ],
            [
                'name' => 'Dewi Lestari',
                'nip' => '202401010000000004',
                'tanggal_lahir' => '2000-11-05',
                'email' => 'dewi@rsd.com',
                'id_jabatan' => 2,
                'id_lokasi' => 2,
                'id_jam_kerja' => 1,
                'status' => 'approved',
            ],
            [
                'name' => 'Rizki Pratama',
                'nip' => '202401010000000005',
                'tanggal_lahir' => '1996-05-18',
                'email' => 'rizki@rsd.com',
                'id_jabatan' => 1,
                'id_lokasi' => 1,
                'id_jam_kerja' => 2,
                'status' => 'rejected',
            ],
        ];

        foreach ($pegawai as $p) {
            $password = Carbon::parse($p['tanggal_lahir'])->format('dmY');

            Pegawai::create([
                'name' => $p['name'],
                'nip' => $p['nip'],
                'tanggal_lahir' => $p['tanggal_lahir'],
                'email' => $p['email'],
                'password' => Hash::make($password),
                'id_jabatan' => $p['id_jabatan'],
                'id_lokasi' => $p['id_lokasi'],
                'id_jam_kerja' => $p['id_jam_kerja'],
                'foto_pegawai' => null,
                'status' => $p['status'],
            ]);
        }
    }
}
