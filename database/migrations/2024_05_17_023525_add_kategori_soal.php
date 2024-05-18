<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kuis', function (Blueprint $table) {
            $table->foreignId('kategori_soal_id')->constrained('kategori_soals');
        });
    }
};
