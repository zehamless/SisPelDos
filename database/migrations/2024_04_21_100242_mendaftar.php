<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mendaftar', function (Blueprint $table) {
            $table->foreignUlid('users_id')->constrained()->onDelete('cascade');
            $table->foreignId('pelatihan_id')->constrained()->onDelete('cascade');
            $table->boolean('status')->default(false);
            $table->jsonb('files')->nullable();
            $table->jsonb('file_name')->nullable();
            $table->text('pesan')->nullable();
            $table->timestamps();
        });
    }
};
