<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;
    protected $table = 'pegawai';
    protected $fillable = [
        'name',
        'nip',
        'email',
        'password',
        'id_jabatan',
        'id_lokasi',
        'id_jam_kerja',
        'foto_pegawai',
        'status',
    ];
    protected $hidden = [
        'password',
    ];
}
