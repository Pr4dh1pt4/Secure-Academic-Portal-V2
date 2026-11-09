<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Seeder akun demo: penguji (dosenpbkk@its.ac.id, peran Admin) dengan 3 proposal
 * portofolio siap presentasi, dan staf akademik (staf@its.ac.id, peran Staf).
 * Peran harus sudah dibuat oleh RolePermissionSeeder.
 *
 * Memakai updateOrCreate sehingga aman dijalankan berulang kali.
 */
class DemoUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Proposal portofolio akun demo, ditulis manual (bukan Faker).
     *
     * Key 'hari_lalu' mengatur tanggal dibuat agar urutan di dashboard rapi.
     *
     * @var list<array{judul: string, deskripsi: string, tema_agent: string, api_key_secure: string, hari_lalu: int}>
     */
    private const PROJECTS = [
        [
            'judul' => 'Platform Agentic AI Otonom untuk Pengujian Aplikasi Web yang Sudah Di-deploy',
            'deskripsi' => 'Agen otonom mengakses aplikasi web yang sudah live, menjalankan pengujian fungsional, '
                .'aksesibilitas, performa, dan keamanan, lalu menelusuri kode terkait di repository Git yang '
                .'terhubung. Agen menyusun perbaikan, memvalidasinya lewat pengujian otomatis agar tidak '
                .'merusak fungsi lain, kemudian membuat pull request terverifikasi untuk direview manusia.',
            'tema_agent' => 'Claude',
            'api_key_secure' => 'sk-demo-qa-agent-7f3c9a1e5b2d4c8f0a6e',
            'hari_lalu' => 1,
        ],
        [
            'judul' => 'Asisten RAG Materi Kuliah PBKK untuk Mahasiswa',
            'deskripsi' => 'RAG agent yang menjawab pertanyaan mahasiswa tentang Laravel (routing, migration, '
                .'Eloquent, middleware) berdasarkan slide dan modul resmi mata kuliah PBKK. Setiap jawaban '
                .'menyertakan rujukan halaman sumber dan dijalankan secara lokal dengan Ollama agar data kuliah '
                .'tidak keluar dari server kampus.',
            'tema_agent' => 'Ollama',
            'api_key_secure' => 'ollama-demo-rag-2b8e4f6a1c3d5e7f9a0b',
            'hari_lalu' => 7,
        ],
        [
            'judul' => 'Multi-Agent Code Reviewer untuk Tugas Laravel',
            'deskripsi' => 'Tim agen CrewAI dengan peran terpisah: reviewer gaya kode (PSR-12), auditor keamanan '
                .'(mass assignment, SQL injection, XSS pada Blade), dan penguji yang menjalankan php artisan test. '
                .'Hasilnya dirangkum menjadi umpan balik terstruktur sebelum tugas dinilai dosen.',
            'tema_agent' => 'CrewAI',
            'api_key_secure' => 'crew-demo-review-9d1f3b5e7a2c4e6f8b0d',
            'hari_lalu' => 14,
        ],
    ];

    /**
     * Buat atau perbarui akun demo beserta project-nya.
     */
    public function run(): void
    {
        // Pastikan peran sudah ada bila seeder ini dijalankan sendirian
        // (lewat DatabaseSeeder, RolePermissionSeeder sudah dipanggil lebih dulu).
        if (! Role::where('name', 'Admin')->exists()) {
            $this->call(RolePermissionSeeder::class);
        }

        $dosen = User::updateOrCreate(
            ['email' => 'dosenpbkk@its.ac.id'],
            [
                'name' => 'Dosen PBKK',
                'program_studi' => 'Teknik Informatika',
                'bio' => 'Akun demo penguji mata kuliah Pemrograman Berbasis Kerangka Kerja (PBKK).',
                'password' => 'password', // Di-hash otomatis oleh cast 'hashed' di model User.
            ],
        );

        // email_verified_at tidak ada di $fillable, jadi diisi terpisah agar lolos middleware 'verified'.
        $dosen->forceFill(['email_verified_at' => $dosen->email_verified_at ?? now()])->save();

        // Dosen penguji memegang peran Admin (akses penuh panel backoffice).
        $dosen->syncRoles(['Admin']);

        // Akun staf akademik: akses terbatas, tidak bisa menghapus data maupun mengatur peran.
        $staf = User::updateOrCreate(
            ['email' => 'staf@its.ac.id'],
            [
                'name' => 'Staf Akademik',
                'program_studi' => 'Teknik Informatika',
                'bio' => 'Akun demo staf tata usaha: mengelola data proposal dan memantau data mahasiswa.',
                'password' => 'password',
            ],
        );
        $staf->forceFill(['email_verified_at' => $staf->email_verified_at ?? now()])->save();
        $staf->syncRoles(['Staf']);

        foreach (self::PROJECTS as $data) {
            $tanggal = now()->subDays($data['hari_lalu']);
            unset($data['hari_lalu']);

            // Lewat relasi, user_id terisi otomatis tanpa perlu masuk $fillable.
            $project = $dosen->projects()->updateOrCreate(['judul' => $data['judul']], $data);
            $project->forceFill(['created_at' => $tanggal, 'updated_at' => $tanggal])->save();
        }
    }
}
