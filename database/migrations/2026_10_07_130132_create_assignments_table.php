<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel assignments untuk menyimpan riwayat tugas kuliah mahasiswa.
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            // BIGINT UNSIGNED, sama dengan users.id. Riwayat tugas ikut terhapus saat user dihapus.
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // VARCHAR untuk nama mata kuliah dan judul tugas yang singkat.
            $table->string('mata_kuliah');
            $table->string('judul');

            // TEXT karena ringkasan tugas bisa panjang dan boleh dikosongkan.
            $table->text('deskripsi')->nullable();

            // VARCHAR(2048) agar muat URL repository/demo yang panjang; tidak semua tugas punya tautan.
            $table->string('tautan', 2048)->nullable();

            // TINYINT UNSIGNED (0-255) cukup untuk nilai 0-100; kosong bila belum dinilai.
            $table->unsignedTinyInteger('nilai')->nullable();

            // DATE (tanpa jam) karena yang dicatat hanya tanggal pengumpulan. Diindeks untuk pengurutan.
            $table->date('dikumpulkan_pada')->index();

            $table->timestamps();
        });
    }

    /**
     * Hapus tabel assignments.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
