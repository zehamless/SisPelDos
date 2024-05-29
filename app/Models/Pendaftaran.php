<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Pendaftaran extends Pivot
{

    protected $table = 'daftarPeserta';
    protected $foreignKey = 'users_id';
    protected $relatedKey = 'pelatihan_id';
}
