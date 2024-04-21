<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use hasFactory;
    protected $fillable = ['tahun_ajar', 'tahun'];

    public function pelatihan()
    {
        return $this->hasMany(Pelatihan::class);
    }
}
