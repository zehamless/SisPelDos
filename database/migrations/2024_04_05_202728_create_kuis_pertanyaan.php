<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kuis_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_tugas_id')->unsigned();
            $table->foreignId('kuis_id')->unsigned();
            $table->unsignedInteger('urutan')->nullable()->index();
            $table->timestamps();
        });
    }
};
