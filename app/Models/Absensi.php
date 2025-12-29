<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'absensi';

    protected $fillable = [
        'id_pegawai',
        'tanggal',
        'waktu_masuk',
        'waktu_keluar',
        'status',
        'keterangan',
        'lokasi_masuk',
        'lokasi_keluar',
        'foto_masuk',
        'foto_keluar',
        'surat'
    ];
}
