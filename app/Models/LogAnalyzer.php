<?php

namespace App\Models;

use App\Models\Concerns\HasToolExecutionLogs;
use Database\Factories\LogAnalyzerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Agent penganalisis log server dan autentikasi.
 *
 * @property int $id
 * @property string $nama
 * @property string $versi
 * @property string $sumber_log
 * @property int $jendela_jam
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class LogAnalyzer extends Model
{
    /** @use HasFactory<LogAnalyzerFactory> */
    use HasFactory, HasToolExecutionLogs;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'versi',
        'sumber_log',
        'jendela_jam',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jendela_jam' => 'integer',
        ];
    }
}
