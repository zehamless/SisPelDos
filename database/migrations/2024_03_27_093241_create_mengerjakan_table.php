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
            $table->foreignUlid('users_id')->constrained()->onDelete('cascade');
            $table->foreignId('materi_tugas_id')->constrained()->onDelete('cascade');
            $table->string('file')->nullable();
            $table->text('pesan')->nullable();
            $table->string('penilaian')->nullable();
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

