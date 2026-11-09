<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Akun pengguna portal: mahasiswa (@student.its.ac.id) atau dosen/staf.
 *
 * MustVerifyEmail mengaktifkan middleware 'verified' Breeze: akun yang emailnya
 * belum diverifikasi tidak bisa membuka dashboard maupun panel admin.
 * HasRoles (Spatie) menyimpan peran dan hak akses di tabel roles/permissions.
 */
#[Fillable(['name', 'email', 'password', 'nrp', 'program_studi', 'bio', 'minat', 'keahlian'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Peran yang boleh masuk panel backoffice (/admin).
     */
    public const PERAN_BACKOFFICE = ['Admin', 'Staf'];

    /**
     * Scope akun mahasiswa (berdomain email student ITS).
     *
     * @param  Builder<User>  $query
     */
    public function scopeMahasiswa(Builder $query): void
    {
        $query->where('email', 'like', '%@student.its.ac.id');
    }

    /**
     * Apakah user ini boleh masuk panel backoffice.
     */
    public function bisaMasukBackoffice(): bool
    {
        return $this->hasAnyRole(self::PERAN_BACKOFFICE);
    }

    /**
     * Keterangan di menu user AdminLTE (config adminlte.usermenu_desc): daftar peran.
     */
    public function adminlte_desc(): string
    {
        $peran = $this->getRoleNames();

        return $peran->isEmpty() ? 'Tanpa peran' : 'Peran: '.$peran->implode(', ');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'minat' => 'array',
            'keahlian' => 'array',
        ];
    }

    /**
     * Apakah akun ini milik mahasiswa (berdomain email student ITS).
     */
    public function isMahasiswa(): bool
    {
        return str_ends_with($this->email, '@student.its.ac.id');
    }

    /**
     * Apakah akun ini pemilik portofolio publik (lihat config/portfolio.php).
     */
    public function isPemilikPortofolio(): bool
    {
        return $this->email === config('portfolio.owner_email');
    }

    /**
     * Inisial nama (maks. 2 huruf) untuk avatar, mis. "PR".
     */
    public function inisial(): string
    {
        return collect(explode(' ', $this->name))
            ->take(2)
            ->map(fn (string $kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
            ->implode('');
    }

    /**
     * Proposal ide proyek Agentic AI milik user ini.
     *
     * @return HasMany<Project, $this>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Riwayat tugas kuliah milik user ini.
     *
     * @return HasMany<Assignment, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Sesi obrolan user ini dengan agent AI.
     *
     * @return HasMany<ChatSession, $this>
     */
    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }
}
