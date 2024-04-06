<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MateriTugas extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'judul',
        'deskripsi',
        'files',
        'file_name',
        'jenis',
        'tipe',
        'tgl_mulai',
        'tgl_selesai',
        'urutan',
    ];

    protected $casts = [
        'files' => 'array',
        'file_name' => 'array',
        'tgl_mulai' => 'datetime',
        'tgl_selesai' => 'datetime',
    ];
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($kuis) {
            $maxUrutan = self::max('urutan');
            $kuis->urutan = $maxUrutan ? $maxUrutan + 1 : 1;
        });
    }
    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function kuis()
    {
        return $this->belongsToMany(Kuis::class, 'kuis_pertanyaan', 'materi_tugas_id', 'kuis_id');
    }
}
