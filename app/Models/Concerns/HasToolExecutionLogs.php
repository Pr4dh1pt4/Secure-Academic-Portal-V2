<?php

namespace App\Models\Concerns;

use App\Models\ToolExecutionLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Sisi "one" dari relasi polymorphic ToolExecutionLog::loggable().
 *
 * Dipakai oleh setiap model agent khusus (SecurityScanner, MalwareScanner, LogAnalyzer).
 */
trait HasToolExecutionLogs
{
    /**
     * Kolom morphs tidak bisa diberi foreign key constraint, jadi cascade
     * dilakukan di level aplikasi: log ikut terhapus saat agent-nya dihapus.
     */
    public static function bootHasToolExecutionLogs(): void
    {
        static::deleting(function (self $agent) {
            $agent->toolExecutionLogs()->delete();
        });
    }

    /**
     * Seluruh log tool yang dieksekusi oleh agent ini.
     *
     * @return MorphMany<ToolExecutionLog, $this>
     */
    public function toolExecutionLogs(): MorphMany
    {
        return $this->morphMany(ToolExecutionLog::class, 'loggable');
    }

    /**
     * Label jenis agent untuk ditampilkan di view, mis. "Security Scanner".
     */
    public function jenisAgent(): string
    {
        return (string) str(class_basename($this))->headline();
    }
}
