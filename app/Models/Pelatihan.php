<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelatihan extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'judul',
        'sampul',
        'slug',
        'published',
        'deskripsi',
        'tgl_mulai',
        'tgl_selesai',
        'jmlh_user',
        'jenis_pelatihan',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'published' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function allTugas(): HasMany
    {
        return $this->hasMany(MateriTugas::class)->orderBy('urutan', 'asc');
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
}
