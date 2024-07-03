<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori_pelatihan';
    protected $fillable = [
        'nama',
    ];

    public function pelatihans()
    {
        return $this->hasMany(Pelatihan::class);
    }
}
