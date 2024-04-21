<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('moduls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatihan_id')->constrained('pelatihans')->onDelete('cascade');
            $table->string('judul');
            $table->boolean('published')->default(false);
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('urutan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
