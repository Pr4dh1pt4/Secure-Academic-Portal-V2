<?php

namespace App\Models;

use App\Models\Concerns\HasToolExecutionLogs;
use Database\Factories\SecurityScannerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Agent pemindai kerentanan aplikasi web.
 *
 * @property int $id
 * @property string $nama
 * @property string $versi
 * @property string $target_url
 * @property string $standar
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SecurityScanner extends Model
{
    /** @use HasFactory<SecurityScannerFactory> */
    use HasFactory, HasToolExecutionLogs;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'versi',
        'target_url',
        'standar',
    ];
}
