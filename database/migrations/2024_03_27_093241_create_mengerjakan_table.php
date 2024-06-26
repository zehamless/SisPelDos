<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mengerjakan', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('users_id')->constrained()->onDelete('cascade');
            $table->foreignId('materi_tugas_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['selesai', 'telat', 'belum'])->default('belum');
            $table->boolean('is_kuis')->default(false);
            $table->jsonb('files')->nullable();
            $table->string('file_name')->nullable();
            $table->text('pesan_peserta')->nullable();
            $table->text('pesan_admin')->nullable();
            $table->string('penilaian')->default('belum dinilai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mengerjakan');
    }
};

