<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Riwayat obrolan user dengan agent AI beserta log tool yang dipanggil agent.
 */
class ChatController extends Controller
{
    /**
     * Daftar sesi obrolan milik user yang login.
     *
     * Jumlah pesan dan log dihitung lewat withCount() (subquery COUNT di query yang sama),
     * bukan $session->messages->count() per baris yang memicu N+1.
     */
    public function index(Request $request): View
    {
        return view('chat.index', [
            'sessions' => $request->user()
                ->chatSessions()
                ->withCount(['messages', 'toolExecutionLogs'])
                ->latest('updated_at')
                ->get(),
        ]);
    }

    /**
     * Detail satu sesi obrolan: seluruh pesan beserta seluruh log tool-nya.
     *
     * Eager loading bertingkat memuat semuanya dalam jumlah query tetap, berapa pun
     * banyaknya pesan dan log:
     *   1. chat_sessions        (sesi, sekaligus memastikan milik user yang login)
     *   2. chat_messages        (WHERE chat_session_id = ?)
     *   3. tool_execution_logs  (WHERE chat_message_id IN (...))
     *   4. satu query per jenis agent yang muncul (WHERE id IN (...)) untuk relasi polymorphic loggable
     *
     * Sesi dicari lewat relasi user, sehingga sesi milik orang lain menghasilkan 404.
     */
    public function show(Request $request, int $chatSession): View
    {
        $session = $request->user()
            ->chatSessions()
            ->with([
                'messages' => fn (HasMany $query) => $query->oldest()->oldest('id'),
                'messages.toolExecutionLogs' => fn (HasMany $query) => $query->oldest('id'),
                'messages.toolExecutionLogs.loggable',
            ])
            ->findOrFail($chatSession);

        return view('chat.show', [
            'session' => $session,
            'totalToken' => $session->messages->sum('token_count'),
            'totalTool' => $session->messages->sum(fn ($message) => $message->toolExecutionLogs->count()),
        ]);
    }
}
