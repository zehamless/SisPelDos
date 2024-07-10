<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Redirect;

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
        'syarat',
        'no_sertifikat',
        'jam_pelatihan',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'published' => 'boolean',
        'syarat' => 'array'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_pelatihan_id');
    }
    public function modul(): HasMany
    {
        return $this->hasMany(Modul::class);
    }

    public function pendaftar()
    {
        return $this->belongsToMany(User::class, 'daftarPeserta', 'pelatihan_id', 'users_id')
            ->wherePivotNotIn('status', ['diterima','selesai'])
            ->withPivot('status', 'pesan', 'files', 'file_name')
            ->withTimestamps();
    }

    public function peserta()
    {
        return $this->belongsToMany(User::class, 'daftarPeserta', 'pelatihan_id', 'users_id')
            ->wherePivotIn('status', ['diterima','selesai'])
            ->withPivot('status', 'pesan', 'files', 'file_name')
            ->withTimestamps();
    }
    public function allTugas()
    {
        return $this->hasManyThrough(MateriTugas::class, Modul::class)->whereNot('jenis', 'materi');
    }
}
