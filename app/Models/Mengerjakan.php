<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Mengerjakan extends Pivot
{
    protected $table = 'mengerjakan';
    protected $foreignKey = 'users_id';
    protected $relatedKey = 'materi_tugas_id';

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    public function materi()
    {
        return $this->belongsTo(MateriTugas::class, 'materi_tugas_id');
    }
}
