<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('materi_tugas', function (Blueprint $table) {
            $table->jsonb('file_name')->nullable()->after('files');
        });
    }

    public function down(): void
    {
        Schema::table('materi_tugas', function (Blueprint $table) {
            //
        });
    }
};
