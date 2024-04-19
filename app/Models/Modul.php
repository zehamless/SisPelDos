<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    protected function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }
}
