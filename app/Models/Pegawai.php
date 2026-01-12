<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Pegawai extends Authenticatable
{
    use HasFactory;
    protected $table = 'pegawai';
    protected $fillable = [
        'name',
        'nip',
        'email',
        'password',
        'tanggal_lahir',
        'id_jabatan',
        'id_lokasi',
        'id_jam_kerja',
        'foto_pegawai',
        'status',
    ];
    protected $hidden = [
        'password',
    ];
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
    public function jamKerja()
    {
        return $this->belongsTo(JamKerja::class, 'id_jam_kerja');
    }
}
