<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Sertifikat extends Pivot
{
    protected $table = 'sertifikats';
    protected $foreignKey = 'users_id';
    protected $relatedKey = 'pelatihan_id';
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }
}
