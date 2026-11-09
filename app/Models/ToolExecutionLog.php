<?php

namespace App\Models;

use Database\Factories\ToolExecutionLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Log satu pemanggilan tool oleh agent AI.
 *
 * Terikat ke pesan yang memicunya (belongsTo ChatMessage) dan, secara polymorphic,
 * ke agent yang mengeksekusinya (SecurityScanner, MalwareScanner, atau LogAnalyzer).
 *
 * @property int $id
 * @property int $chat_message_id
 * @property string $loggable_type Alias morph map, mis. "security_scanner".
 * @property int $loggable_id
 * @property string $tool_name
 * @property array<string, mixed>|null $input
 * @property array<string, mixed>|null $output
 * @property string $status
 * @property int $durasi_ms
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ChatMessage $chatMessage
 * @property-read SecurityScanner|MalwareScanner|LogAnalyzer $loggable
 */
class ToolExecutionLog extends Model
{
    /** @use HasFactory<ToolExecutionLogFactory> */
    use HasFactory;

    /**
     * Sengaja tanpa chat_message_id dan kolom loggable: keduanya diisi lewat relasi
     * ($message->toolExecutionLogs()->make(...)->loggable()->associate($agent)).
     *
     * @var list<string>
     */
    protected $fillable = [
        'tool_name',
        'input',
        'output',
        'status',
        'durasi_ms',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'input' => 'array',
            'output' => 'array',
            'durasi_ms' => 'integer',
        ];
    }

    /**
     * Pesan obrolan yang memicu pemanggilan tool ini.
     *
     * @return BelongsTo<ChatMessage, $this>
     */
    public function chatMessage(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class);
    }

    /**
     * Agent yang mengeksekusi tool ini (polymorphic).
     *
     * @return MorphTo<Model, $this>
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }
}
