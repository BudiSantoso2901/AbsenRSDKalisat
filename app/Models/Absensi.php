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
        'waktu_pulang',
        'status',
        'keterangan',
        'foto_masuk',
        'foto_pulang',
        'latitude',
        'longitude',
        'surat',
        'alasan_edit',
        'edited_by',
        'edited_at',
    ];
    protected $casts = [
        'tanggal' => 'date',
    ];
    // Absensi.php
    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
    // Absensi.php
    public function getEditedByNameAttribute()
    {
        return $this->editor->name ?? '-';
    }
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}
