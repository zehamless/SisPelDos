<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daftarPeserta', function (Blueprint $table) {
            $table->foreignUlid('users_id')->constrained()->onDelete('cascade');
            $table->foreignId('pelatihan_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai', 'tidak_selesai'])->default('pending');
            $table->string('files')->nullable();
            $table->string('file_name')->nullable();
            $table->text('pesan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('daftarPeserta');
    }
};
