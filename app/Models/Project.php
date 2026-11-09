<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Proposal ide proyek Agentic AI milik seorang mahasiswa.
 *
 * @property int $id
 * @property int $user_id
 * @property string $judul
 * @property string|null $deskripsi
 * @property list<array{judul: string, keterangan: string}>|null $tahapan
 * @property string $tema_agent
 * @property string $api_key_secure Plaintext saat dibaca dari model, terenkripsi di database.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * Kolom yang boleh diisi lewat mass assignment.
     *
     * Sengaja tanpa user_id: kepemilikan diisi lewat relasi
     * ($user->projects()->create(...)), sehingga request tidak bisa
     * menyisipkan user_id milik orang lain.
     *
     * @var list<string>
     */
    protected $fillable = [
        'judul',
        'deskripsi',
        'tahapan',
        'tema_agent',
        'api_key_secure',
    ];

    /**
     * Kolom yang disembunyikan saat model diubah ke array/JSON.
     *
     * @var list<string>
     */
    protected $hidden = [
        'api_key_secure',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Dienkripsi dengan APP_KEY saat disimpan, didekripsi saat dibaca.
            'api_key_secure' => 'encrypted',
            'tahapan' => 'array',
        ];
    }

    /**
     * Mahasiswa pemilik proposal ini.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * API key dalam bentuk ter-mask untuk ditampilkan di view, mis. "sk-****abcd".
     */
    public function maskedApiKey(): string
    {
        $key = $this->api_key_secure;
        $prefix = str_contains($key, '-') ? strstr($key, '-', true).'-' : '';

        return $prefix.'****'.substr($key, -4);
    }
}
