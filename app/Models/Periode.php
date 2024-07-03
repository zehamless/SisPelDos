<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use hasFactory;
    protected $fillable = ['tahun'];

    public function pelatihan()
    {
        return $this->hasMany(Pelatihan::class);
    }
    public function peserta()
    {
        return $this->hasManyThrough(Pendaftaran::class, Pelatihan::class)
            ->whereNotIn('status', ['ditolak', 'pending']);
    }
}
