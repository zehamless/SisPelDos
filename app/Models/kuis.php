<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class kuis extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'pertanyaan',
        'jawaban_option',
        'jawaban_benar',
    ];


    protected $casts = [
        'pertanyaan' => 'array',
        'jawaban_option' => 'array',
        'jawaban_benar' => 'array',
    ];

    public function materiTugas()
    {
        return $this->belongsToMany(MateriTugas::class, 'kuis_pertanyaan', 'kuis_id', 'materi_tugas_id');
    }
}
