<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chatbot_datas', function (Blueprint $table) {
            $table->id();
            $table->text('question')->unique();
            $table->boolean('admin')->default(false);
            $table->longText('answer');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_datas');
    }
};
