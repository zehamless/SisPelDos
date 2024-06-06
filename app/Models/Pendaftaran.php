<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Pendaftaran extends Pivot
{

    protected $table = 'daftarPeserta';
    protected $foreignKey = 'users_id';
    protected $relatedKey = 'pelatihan_id';

    public function scopeMendaftar(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'diterima', 'ditolak']);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class, 'pelatihan_id');
    }
}
