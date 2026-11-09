<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder akun pemilik portofolio publik: profil diri, 2 proposal Agentic AI,
 * dan riwayat tugas PBKK. Menggantikan data statis di PageController minggu lalu.
 *
 * Memakai updateOrCreate sehingga aman dijalankan berulang kali.
 */
class PortfolioOwnerSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Proposal Agentic AI milik pemilik portofolio. Proposal pertama (unggulan)
     * dilengkapi tahapan alur kerja untuk diagram di halaman Ide-Riset.
     *
     * @var list<array<string, mixed>>
     */
    private const PROJECTS = [
        [
            'judul' => 'AutoQA Agent',
            'deskripsi' => 'Autonomous web agent berbasis AI yang menguji aplikasi web yang sudah dideploy, mendeteksi isu '
                .'fungsional, aksesibilitas, performa, dan keamanan, lalu menganalisis source code terkait dari repository '
                .'Git yang terhubung. Agent kemudian menghasilkan perbaikan (fix), memvalidasinya lewat automated testing, '
                .'dan membuat verified pull request untuk direview manusia.',
            'tahapan' => [
                ['judul' => 'Web App Under Test', 'keterangan' => 'Agent mengakses aplikasi yang sudah live/deployed.'],
                ['judul' => 'Testing & Issue Detection', 'keterangan' => 'Scan fungsional, aksesibilitas, performa, dan keamanan.'],
                ['judul' => 'Source Code Analysis', 'keterangan' => 'Menelusuri kode terkait di repository Git yang terhubung.'],
                ['judul' => 'Fix Generation', 'keterangan' => 'Menyusun perbaikan kode untuk isu yang ditemukan.'],
                ['judul' => 'Automated Validation', 'keterangan' => 'Fix diuji ulang agar tidak merusak fungsi lain.'],
                ['judul' => 'Verified Pull Request', 'keterangan' => 'PR berisi fix tervalidasi, siap direview manusia.'],
            ],
            'tema_agent' => 'Claude',
            'api_key_secure' => 'sk-demo-autoqa-4e1a7c9b3d5f2a8e6c0b',
            'hari_lalu' => 2,
        ],
        [
            'judul' => 'Agen Pemantau Kualitas Data Pipeline',
            'deskripsi' => 'Tool-using agent yang memantau pipeline ETL, mendeteksi anomali skema dan data kosong, '
                .'menelusuri penyebabnya dari log job, lalu mengusulkan perbaikan query atau konfigurasi sebelum '
                .'data rusak masuk ke dashboard analitik.',
            'tahapan' => null,
            'tema_agent' => 'Ollama',
            'api_key_secure' => 'ollama-demo-pipeline-8b2d6f0a4c1e3a5f7d9b',
            'hari_lalu' => 20,
        ],
    ];

    /**
     * Riwayat tugas PBKK sepanjang semester. Nilai dikosongkan karena belum diumumkan.
     *
     * @var list<array{mata_kuliah: string, judul: string, deskripsi: string, tautan: string|null, dikumpulkan_pada: string}>
     */
    private const ASSIGNMENTS = [
        [
            'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
            'judul' => 'Tugas 1: Sistem Informasi Statik Profil Mahasiswa (Kelompok 7)',
            'deskripsi' => 'Website profil akademik ITS berbasis Laravel sesuai PRD kelompok, dengan halaman statis dan routing dasar.',
            'tautan' => 'https://github.com/Renggosakti/PBKK_Tugas-1_Kelompok-7',
            'dikumpulkan_pada' => '2026-09-08',
        ],
        [
            'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
            'judul' => 'Routing Sandbox Profil Akademik (Individu)',
            'deskripsi' => 'Eksplorasi routing dan controller Laravel: halaman dashboard, profil, data IPK, dan rancangan ide agen.',
            'tautan' => 'https://github.com/Pr4dh1pt4/Website-Pemrograman-Berbasis-Kerangka-Kerja',
            'dikumpulkan_pada' => '2026-09-14',
        ],
        [
            'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
            'judul' => 'Secure Feedback Hub (Form GET/POST)',
            'deskripsi' => 'Portal umpan balik mahasiswa dengan validasi Form Request, proteksi CSRF, dan captcha penjumlahan.',
            'tautan' => null,
            'dikumpulkan_pada' => '2026-09-21',
        ],
        [
            'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
            'judul' => 'Tugas 4: Aplikasi Multi-View Profil Akademik',
            'deskripsi' => 'Master layout Blade, komponen anonim, dan halaman Ide-Riset yang memvisualisasikan pipeline Agentic AI.',
            'tautan' => 'https://github.com/Pr4dh1pt4/PBKK-Agentic-AI',
            'dikumpulkan_pada' => '2026-09-24',
        ],
        [
            'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
            'judul' => 'Secure Academic Portal Database',
            'deskripsi' => 'Memindahkan data portofolio dari variabel controller ke MySQL: migration, foreign key cascade, $fillable, factory, dan seeder.',
            'tautan' => null,
            'dikumpulkan_pada' => '2026-10-07',
        ],
    ];

    /**
     * Buat atau perbarui akun pemilik portofolio beserta proposal dan riwayat tugasnya.
     */
    public function run(): void
    {
        $owner = User::updateOrCreate(
            ['email' => config('portfolio.owner_email')],
            [
                'name' => 'Pradhipta Raja Mahendra',
                'nrp' => '5025241055',
                'program_studi' => 'Teknik Informatika',
                'bio' => 'Mahasiswa semester 5 Teknik Informatika ITS yang sedang menempuh mata kuliah Pemrograman '
                    .'Berbasis Kerangka Kerja (PBKK), tertarik pada pengembangan web dan kecerdasan buatan.',
                'minat' => ['Data Engineering', 'Competitive Programming', 'Software Engineering'],
                'keahlian' => ['PHP & Laravel', 'Python', 'C++', 'SQL', 'Git', 'Tailwind CSS'],
                'password' => 'password', // Di-hash otomatis oleh cast 'hashed' di model User.
            ],
        );

        $owner->forceFill(['email_verified_at' => $owner->email_verified_at ?? now()])->save();

        foreach (self::PROJECTS as $data) {
            $tanggal = now()->subDays($data['hari_lalu']);
            unset($data['hari_lalu']);

            // Lewat relasi, user_id terisi otomatis tanpa perlu masuk $fillable.
            $project = $owner->projects()->updateOrCreate(['judul' => $data['judul']], $data);
            $project->forceFill(['created_at' => $tanggal, 'updated_at' => $tanggal])->save();
        }

        foreach (self::ASSIGNMENTS as $data) {
            $owner->assignments()->updateOrCreate(['judul' => $data['judul']], $data);
        }
    }
}
