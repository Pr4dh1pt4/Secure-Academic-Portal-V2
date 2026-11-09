<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel chat_messages: isi percakapan di dalam sebuah sesi obrolan.
     */
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();

            // chat_sessions (1) -> (N) chat_messages. Pesan ikut terhapus saat sesinya dihapus.
            $table->foreignId('chat_session_id')
                ->constrained('chat_sessions')
                ->onDelete('cascade');

            // Peran pengirim pesan mengikuti format API LLM pada umumnya.
            $table->enum('role', ['system', 'user', 'assistant', 'tool']);

            // TEXT karena jawaban agent bisa jauh lebih panjang dari 255 karakter.
            $table->text('content');

            // Jumlah token pesan ini, berguna untuk memantau biaya pemakaian LLM.
            $table->unsignedInteger('token_count')->default(0);

            $table->timestamps();

            // Pesan selalu dibaca per sesi sesuai urutan waktu.
            $table->index(['chat_session_id', 'created_at']);
        });
    }

    /**
     * Hapus tabel chat_messages.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
