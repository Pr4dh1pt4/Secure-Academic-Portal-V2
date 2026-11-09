<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel chat_sessions: satu baris per sesi obrolan user dengan agent AI.
     */
    public function up(): void
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();

            // users (1) -> (N) chat_sessions. Sesi ikut terhapus saat user dihapus.
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // VARCHAR(255) untuk judul singkat yang tampil di daftar riwayat obrolan.
            $table->string('judul');

            // Model LLM yang menjawab di sesi ini, mis. "claude-opus-5-5" atau "llama3.1:8b".
            $table->string('model_ai')->default('claude-opus-5-5');

            // Sesi yang diarsipkan tidak dihapus, hanya disembunyikan dari daftar aktif.
            $table->enum('status', ['aktif', 'diarsipkan'])->default('aktif');

            $table->timestamps();

            // Daftar riwayat selalu difilter per user dan diurutkan dari yang terbaru.
            $table->index(['user_id', 'updated_at']);
        });
    }

    /**
     * Hapus tabel chat_sessions.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
