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
        'syarat'
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

    public function modul(): HasMany
    {
        return $this->hasMany(Modul::class);
    }

    public function pendaftar()
    {
        return $this->belongsToMany(User::class, 'daftarPeserta', 'pelatihan_id', 'users_id')
            ->wherePivotNotIn('status', ['diterima'])
            ->withPivot('status', 'pesan', 'files', 'file_name', 'nama', 'role')
            ->withTimestamps();
    }

    public function peserta()
    {
        return $this->belongsToMany(User::class, 'daftarPeserta', 'pelatihan_id', 'users_id')
            ->wherePivot('status', 'diterima')
            ->withPivot('status', 'pesan', 'files', 'file_name', 'nama', 'role')
            ->withTimestamps();
    }

}
