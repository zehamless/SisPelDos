<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class kuis extends Model
{
    use SoftDeletes, HasFactory,Cloneable;

    protected $fillable = [
        'kategori_soal_id',
        'pertanyaan',
        'jawaban',
        'tipe',
//        'jawaban_option',
//        'jawaban_benar',
    ];


    protected $casts = [
        'pertanyaan' => 'array',
//        'jawaban_option' => 'array',
//        'jawaban_benar' => 'array',
        'jawaban' => 'array',
    ];

    public function materiTugas()
    {
        return $this->belongsToMany(MateriTugas::class, 'kuis_pertanyaan', 'kuis_id', 'materi_tugas_id');
    }

    public function kategori()
    {
        return $this->belongsTo(kategoriSoal::class, 'kategori_soal_id');
    }

}
