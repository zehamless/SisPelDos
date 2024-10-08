<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class kategoriSoal extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'kategori',
    ];

    public function kuis()
    {
        return $this->hasMany(kuis::class);
    }
}
