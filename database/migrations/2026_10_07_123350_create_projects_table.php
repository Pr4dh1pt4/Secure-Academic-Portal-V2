<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel projects untuk menyimpan proposal ide proyek Agentic AI milik mahasiswa.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // BIGINT UNSIGNED, sama dengan users.id. Project otomatis ikut terhapus saat user dihapus.
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // VARCHAR(255) cukup untuk judul proposal yang singkat dan bisa diindeks.
            $table->string('judul');

            // TEXT karena deskripsi bisa panjang (lebih dari 255 karakter) dan boleh dikosongkan.
            $table->text('deskripsi')->nullable();

            // VARCHAR untuk nama provider/framework agen. Jika tidak diisi, dipakai Ollama.
            $table->string('tema_agent')->default('Ollama');

            // TEXT, bukan VARCHAR: nilai disimpan dalam bentuk terenkripsi (cast 'encrypted'),
            // dan payload base64 hasil enkripsi jauh lebih panjang dari 255 karakter.
            $table->text('api_key_secure');

            $table->timestamps();
        });
    }

    /**
     * Hapus tabel projects.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
