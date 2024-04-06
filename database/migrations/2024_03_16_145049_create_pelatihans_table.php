<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pelatihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periodes');
            $table->string('judul');
            $table->boolean('published')->default(false);
            $table->string('sampul')->nullable();
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->unsignedBigInteger('jmlh_user')->nullable();
            $table->index(['slug', 'tgl_mulai', 'tgl_selesai']);
            $table->enum('jenis_pelatihan', ['dosen_lokal', 'dosen_luar', 'semua'])->default('semua');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelatihans');
    }
};
