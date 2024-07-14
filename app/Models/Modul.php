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
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($modul) {
            $maxUrutan = self::max('urutan');
            $modul->urutan = $maxUrutan ? $maxUrutan + 1 : 1;
        });
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function materi(): HasMany
    {
        return $this->hasMany(MateriTugas::class)->where('jenis', 'materi');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(MateriTugas::class)->where('jenis', 'tugas');
    }
    public function diskusi(): HasMany
    {
        return $this->hasMany(MateriTugas::class)->where('jenis', 'diskusi');
    }
    public function kuis(): HasMany
    {
        return $this->hasMany(MateriTugas::class)->where('jenis', 'kuis');
    }

    public function allTugas(): HasMany
    {
        return $this->hasMany(MateriTugas::class);
    }
    public function noMateri()
    {
        return $this->allTugas()->whereNot('jenis', 'materi')->where('published', true);
    }
    public function pengajar()
    {
        return $this->belongsToMany(User::class, 'pengajar_modul', 'modul_id', 'user_id')
            ->where('role', 'pengajar')
            ->withTimestamps();

    }
}
