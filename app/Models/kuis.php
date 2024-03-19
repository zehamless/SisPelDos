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
        'jawaban',
    ];

    protected $casts = [
        'pertanyaan' => 'array',
        'jawaban' => 'array',
    ];

    protected function materiTugas(): BelongsTo
    {
        return $this->belongsTo(MateriTugas::class);
    }
}
