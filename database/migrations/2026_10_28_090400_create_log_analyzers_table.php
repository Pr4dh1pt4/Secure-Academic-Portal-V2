<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel log_analyzers: agent penganalisis log server dan autentikasi.
     */
    public function up(): void
    {
        Schema::create('log_analyzers', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('versi', 20);

            // Sumber log yang dianalisis, mis. "nginx/access.log".
            $table->string('sumber_log');

            // Rentang waktu analisis dalam jam ke belakang.
            $table->unsignedSmallInteger('jendela_jam')->default(24);

            $table->timestamps();
        });
    }

    /**
     * Hapus tabel log_analyzers.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_analyzers');
    }
};
