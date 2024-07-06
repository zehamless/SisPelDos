<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->foreignId('kategori_pelatihan_id')->constrained('kategori_pelatihan');
        });
    }

    public function down(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->dropForeign(['kategori_pelatihan_id']);
            $table->dropColumn('kategori_pelatihan_id');
        });
    }
};
