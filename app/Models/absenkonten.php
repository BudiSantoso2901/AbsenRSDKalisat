<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class absenkonten extends Model
{
    use HasFactory;
    protected $table = 'absenkontens';
    protected $fillable = [
        'id_pegawai',
        'tanggal',
        'bukti_foto',
        'link_fb',
        'link_ig',
        'link_tiktok',
        'verified_by',
        'keterangan',
        'status_verifikasi',
        'id_ruangan',
    ];
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    public function ruangan()
    {
        return $this->belongsTo(ruangan::class, 'id_ruangan');
    }
}
