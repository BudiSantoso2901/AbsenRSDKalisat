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
        'shift_id',
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
    public function shift()
    {
        return $this->belongsTo(JamKerja::class, 'shift_id');
    }
    public function getTlBadgeAttribute()
    {
        $shift = $this->shift ?? $this->pegawai->jamKerja ?? null;

        if ($this->status !== 'hadir' || !$this->waktu_masuk || !$shift) {
            return null;
        }

        $tanggal = \Carbon\Carbon::parse($this->tanggal)->toDateString();
        $waktuMasuk = \Carbon\Carbon::parse($this->waktu_masuk);

        $jamMulai = \Carbon\Carbon::parse($tanggal . ' ' . $shift->jam_mulai);
        $jamSelesai = \Carbon\Carbon::parse($tanggal . ' ' . $shift->jam_selesai);

        // SHIFT MALAM
        if ($shift->jam_selesai < $shift->jam_mulai) {
            $jamSelesai->addDay();

            if ($waktuMasuk->lt($jamMulai)) {
                $jamMulai->subDay();
                $jamSelesai->subDay();
            }
        }

        $toleransi = $shift->toleransi_menit ?? 0;
        $jamMulaiToleransi = $jamMulai->copy()->addMinutes($toleransi);

        if ($waktuMasuk->gt($jamMulaiToleransi)) {
            $menitTelat = $jamMulaiToleransi->diffInMinutes($waktuMasuk);

            if ($menitTelat <= 30) return 'TL1';
            if ($menitTelat <= 60) return 'TL2';
            if ($menitTelat <= 90) return 'TL3';
            return 'TL4';
        }

        return null;
    }
}
