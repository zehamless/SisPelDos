<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modul extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'urutan',
        'published',
        'deskripsi',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function materi(): HasMany
    {
        return $this->hasMany(MateriTugas::class)->where('jenis', 'materi');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(MateriTugas::class)->where([
            ['jenis', '=', 'tugas'],
        ]);
    }

    public function kuis(): HasMany
    {
        return $this->hasMany(MateriTugas::class)->where([
            ['jenis', '=', 'kuis'],
        ]);
    }

    public function allTugas(): HasMany
    {
        return $this->hasMany(MateriTugas::class);
    }

}
