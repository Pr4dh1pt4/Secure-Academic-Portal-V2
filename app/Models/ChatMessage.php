<?php

namespace App\Models;

use Database\Factories\ChatMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Satu pesan di dalam sesi obrolan (dari user, agent, sistem, atau hasil tool).
 *
 * @property int $id
 * @property int $chat_session_id
 * @property string $role
 * @property string $content
 * @property int $token_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ChatSession $chatSession
 */
class ChatMessage extends Model
{
    /** @use HasFactory<ChatMessageFactory> */
    use HasFactory;

    /**
     * Sengaja tanpa chat_session_id: pesan dibuat lewat $session->messages()->create(...).
     *
     * @var list<string>
     */
    protected $fillable = [
        'role',
        'content',
        'token_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'token_count' => 'integer',
        ];
    }

    /**
     * Sesi obrolan tempat pesan ini berada.
     *
     * @return BelongsTo<ChatSession, $this>
     */
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    /**
     * Log pemanggilan tool yang dipicu oleh pesan ini.
     *
     * @return HasMany<ToolExecutionLog, $this>
     */
    public function toolExecutionLogs(): HasMany
    {
        return $this->hasMany(ToolExecutionLog::class);
    }
}
