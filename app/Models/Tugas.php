<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Tugas extends Pivot
{
    protected $table = 'mengerjakan';
    protected $foreignKey = 'materi_tugas_id';
    protected $relatedKey = 'users_id';
    public $incrementing = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    public function modul()
    {
        return $this->belongsTo(MateriTugas::class, 'materi_tugas_id');
    }
}
