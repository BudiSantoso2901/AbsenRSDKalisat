<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamKerja extends Model
{
    use HasFactory;
    protected $table = 'jam_kerja';
    protected $fillable = [
        'nama_jam_kerja',
        'jam_mulai',
        'jam_selesai',
        'toleransi_menit',
    ];
}
