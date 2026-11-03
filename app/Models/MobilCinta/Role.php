<?php

namespace App\Models\MobilCinta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $table = 'mobil_cinta_peran';

    protected $fillable = ['nama_peran'];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'peran_id');
    }
}
