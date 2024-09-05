<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotDatas extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'admin',
    ];
    protected $casts = [
        'admin' => 'boolean',
    ];

    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('admin', true);
    }
}
