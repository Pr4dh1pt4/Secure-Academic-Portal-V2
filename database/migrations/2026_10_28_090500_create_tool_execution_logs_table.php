<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel tool_execution_logs: log setiap pemanggilan tool oleh agent AI.
     */
    public function up(): void
    {
        Schema::create('tool_execution_logs', function (Blueprint $table) {
            $table->id();

            // chat_messages (1) -> (N) tool_execution_logs. Log ikut terhapus saat pesannya dihapus.
            $table->foreignId('chat_message_id')
                ->constrained('chat_messages')
                ->onDelete('cascade');

            // Polymorphic: loggable_type + loggable_id (+ index gabungan) menunjuk agent
            // yang mengeksekusi tool, bisa SecurityScanner, MalwareScanner, atau LogAnalyzer.
            $table->morphs('loggable');

            // Nama fungsi tool yang dipanggil, mis. "scan_owasp_top10".
            $table->string('tool_name');

            // JSON agar argumen dan hasil tool yang bentuknya bervariasi tetap terstruktur.
            $table->json('input')->nullable();
            $table->json('output')->nullable();

            $table->enum('status', ['berhasil', 'gagal', 'timeout'])->default('berhasil');

            // Durasi eksekusi dalam milidetik untuk memantau tool yang lambat.
            $table->unsignedInteger('durasi_ms')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Hapus tabel tool_execution_logs.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_execution_logs');
    }
};
