<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ruangan extends Model
{
    use HasFactory;
    protected $table = 'ruangans';
    protected $fillable = [
        'nama_ruangan',
    ];
    public function absensikontens()
    {
        return $this->hasMany(absenkonten::class, 'id_ruangan');
    }
}
