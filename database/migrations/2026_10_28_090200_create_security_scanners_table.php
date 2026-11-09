<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel security_scanners: agent pemindai kerentanan aplikasi web.
     */
    public function up(): void
    {
        Schema::create('security_scanners', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('versi', 20);

            // Target yang dipindai, mis. URL aplikasi yang sudah di-deploy.
            $table->string('target_url');

            // Standar acuan pemindaian, mis. "OWASP Top 10 2025".
            $table->string('standar')->default('OWASP Top 10');

            $table->timestamps();
        });
    }

    /**
     * Hapus tabel security_scanners.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_scanners');
    }
};
