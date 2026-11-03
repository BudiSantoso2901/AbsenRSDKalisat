<?php

namespace App\Models\MobilCinta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'mobil_cinta_akun';

    protected $fillable = ['peran_id', 'nama', 'nama_pengguna', 'kata_sandi', 'status_aktif', 'login_terakhir_pada'];

    protected $hidden = ['kata_sandi', 'password', 'remember_token'];

    protected $casts = [
        'kata_sandi' => 'hashed',
        'status_aktif' => 'boolean',
        'login_terakhir_pada' => 'datetime',
    ];

    public function getAuthPassword(): string
    {
        return (string) $this->kata_sandi;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'peran_id');
    }

    public function journeysRequested(): HasMany
    {
        return $this->hasMany(Journey::class, 'diajukan_oleh_id');
    }

    public function journeysDriven(): HasMany
    {
        return $this->hasMany(Journey::class, 'sopir_id');
    }

    public function journeysCoordinated(): HasMany
    {
        return $this->hasMany(Journey::class, 'ditugaskan_oleh_id');
    }

}
