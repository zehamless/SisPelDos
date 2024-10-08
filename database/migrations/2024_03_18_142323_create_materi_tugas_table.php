<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materi_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('moduls')->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->jsonb('files')->nullable();
            $table->string('jenis');
            $table->boolean('published')->default(false);
            $table->dateTime('tgl_mulai')->nullable();
            $table->dateTime('tgl_tenggat')->nullable();
            $table->dateTime('tgl_selesai')->nullable();
            $table->boolean('terjadwal')->default(false);
            $table->unsignedInteger('durasi')->nullable();
            $table->unsignedInteger('urutan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_tugas');
    }
};
