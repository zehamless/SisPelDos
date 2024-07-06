<?php

namespace App\Models;

use App\Jobs\AttachUser_MateriTugasJob;
use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MateriTugas extends Model
{
    use SoftDeletes, HasFactory, Cloneable;

//    protected $cloneable_relations = ['kuis'];
    protected $fillable = [
        'judul',
        'deskripsi',
        'files',
        'file_name',
        'jenis',
        'status',
        'published',
        'terjadwal',
        'tgl_tenggat',
        'tgl_mulai',
        'tgl_selesai',
        'urutan',
    ];

    protected $casts = [
        'files' => 'array',
        'file_name' => 'array',
        'tgl_mulai' => 'datetime',
        'tgl_selesai' => 'datetime',
        'tgl_tenggat' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($kuis) {
            $maxUrutan = self::max('urutan');
            $kuis->urutan = $maxUrutan ? $maxUrutan + 1 : 1;
        });
        static::replicating(function ($kuis) {
            $kuis->published = false;
        });
        static::created(function ($kuis) {
            if ($kuis->jenis !== 'materi') {
                dispatch(new AttachUser_MateriTugasJob($kuis, $kuis->modul->pelatihan->peserta));
            }
        });
    }

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function modul(): BelongsTo
    {
        return $this->belongsTo(Modul::class)->orderBy('created_at', 'desc');
    }

    public function kuis()
    {
        return $this->belongsToMany(Kuis::class, 'kuis_pertanyaan', 'materi_tugas_id', 'kuis_id');
    }

    public function peserta()
    {
        return $this->belongsToMany(User::class, 'mengerjakan', 'materi_tugas_id', 'users_id')
            ->withPivot('penilaian', 'is_kuis', 'created_at', 'updated_at', 'id', 'status')
            ->withTimestamps();
    }


    public function mengerjakanKuis()
    {
        return $this->peserta()->where('is_kuis', true)->withPivot('created_at', 'updated_at');
    }

    public function mengerjakanTugas()
    {
        return $this->peserta()->wherePivot('is_kuis', false)->withPivot('created_at', 'files', 'file_name', 'updated_at');
    }

    public function scopePeserta(Builder $query): Builder
    {
        return $this->peserta()->where();
    }

    public function scopeMateri(Builder $query): Builder
    {
        return $this->where('jenis', 'materi');
    }

    public function scopeTugas(Builder $query): Builder
    {
        return $this->where('jenis', 'tugas');
    }

    public function scopeTerjadwal(Builder $query): Builder
    {
        return $query->where('terjadwal', true)->where('published', false)->whereTime('tgl_mulai', '<=', now())->whereTime('tgl_selesai', '>=', now());
    }

}
