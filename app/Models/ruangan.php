<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
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
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_ruangan',
            'id_ruangan',
            'id_user'
        )->withTimestamps();
    }
}
