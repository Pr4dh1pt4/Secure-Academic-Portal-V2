<?php

namespace App\Models;

use Database\Factories\AssignmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Riwayat tugas kuliah milik seorang mahasiswa.
 *
 * @property int $id
 * @property int $user_id
 * @property string $mata_kuliah
 * @property string $judul
 * @property string|null $deskripsi
 * @property string|null $tautan
 * @property int|null $nilai
 * @property Carbon $dikumpulkan_pada
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
class Assignment extends Model
{
    /** @use HasFactory<AssignmentFactory> */
    use HasFactory;

    /**
     * Kolom yang boleh diisi lewat mass assignment.
     *
     * Sengaja tanpa user_id: kepemilikan diisi lewat relasi
     * ($user->assignments()->create(...)).
     *
     * @var list<string>
     */
    protected $fillable = [
        'mata_kuliah',
        'judul',
        'deskripsi',
        'tautan',
        'nilai',
        'dikumpulkan_pada',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
            'dikumpulkan_pada' => 'date',
        ];
    }

    /**
     * Mahasiswa pemilik riwayat tugas ini.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
