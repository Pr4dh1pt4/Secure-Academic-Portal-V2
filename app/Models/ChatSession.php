<?php

namespace App\Models;

use Database\Factories\ChatSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * Sesi obrolan seorang user dengan agent AI.
 *
 * @property int $id
 * @property int $user_id
 * @property string $judul
 * @property string $model_ai
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
class ChatSession extends Model
{
    /** @use HasFactory<ChatSessionFactory> */
    use HasFactory;

    /**
     * Sengaja tanpa user_id: sesi dibuat lewat $user->chatSessions()->create(...).
     *
     * @var list<string>
     */
    protected $fillable = [
        'judul',
        'model_ai',
        'status',
    ];

    /**
     * User pemilik sesi obrolan ini.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Seluruh pesan di sesi ini.
     *
     * @return HasMany<ChatMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Seluruh log tool di sesi ini, melewati tabel chat_messages.
     *
     * @return HasManyThrough<ToolExecutionLog, ChatMessage, $this>
     */
    public function toolExecutionLogs(): HasManyThrough
    {
        return $this->hasManyThrough(ToolExecutionLog::class, ChatMessage::class);
    }
}
