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
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nama');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('no_induk')->nullable()->after('id');
            $table->unsignedInteger('no_hp')->nullable()->after('email');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('universitas')->nullable();
            $table->string('prodi')->nullable();
            $table->string('jabatan_fungsional')->nullable();
            $table->string('pendidikan_tertinggi')->nullable();
            $table->string('status_kerja')->nullable();
            $table->string('status_dosen')->nullable();
            $table->string('role')->default('external')->index();
            $table->string('nama_gelar');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
