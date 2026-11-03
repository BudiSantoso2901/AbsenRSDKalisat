<?php

namespace App\Models\MobilCinta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'mobil_cinta_kendaraan';

    protected $fillable = ['nomor_polisi'];

    public function journeys(): HasMany
    {
        return $this->hasMany(Journey::class, 'kendaraan_id');
    }
}
