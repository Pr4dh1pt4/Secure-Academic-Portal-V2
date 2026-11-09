<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory proposal ide proyek Agentic AI yang realistis (bukan lorem ipsum).
 *
 * Judul dan deskripsi dirangkai dari domain, jenis agen, dan tujuan konkret
 * yang memang relevan dengan domainnya, sehingga setiap kombinasi tetap masuk akal.
 *
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Domain beserta pengguna sasaran dan tujuan konkret yang relevan.
     *
     * @var array<string, array{pengguna: string, tujuan: list<string>}>
     */
    private const DOMAIN = [
        'Pendidikan' => [
            'pengguna' => 'dosen dan mahasiswa',
            'tujuan' => [
                'menyusun bank soal adaptif dari materi perkuliahan',
                'memberi umpan balik otomatis pada tugas pemrograman',
                'merekomendasikan jalur belajar sesuai capaian mahasiswa',
            ],
        ],
        'Kesehatan' => [
            'pengguna' => 'tenaga medis puskesmas',
            'tujuan' => [
                'melakukan triase awal keluhan pasien',
                'merangkum rekam medis sebelum konsultasi',
                'memantau kepatuhan minum obat pasien kronis',
            ],
        ],
        'Logistik' => [
            'pengguna' => 'operator gudang dan kurir',
            'tujuan' => [
                'mengoptimalkan rute pengiriman harian',
                'memprediksi kebutuhan restock inventaris',
                'mendeteksi keterlambatan pengiriman lebih awal',
            ],
        ],
        'Keuangan' => [
            'pengguna' => 'pelaku UMKM',
            'tujuan' => [
                'mengategorikan transaksi dan menyusun laporan arus kas',
                'mendeteksi transaksi mencurigakan secara real-time',
                'menyusun simulasi anggaran dan proyeksi keuntungan',
            ],
        ],
        'Pertanian' => [
            'pengguna' => 'petani dan penyuluh pertanian',
            'tujuan' => [
                'mendiagnosis penyakit tanaman dari foto daun',
                'menjadwalkan irigasi berdasarkan data cuaca dan sensor tanah',
                'memperkirakan waktu panen dan harga pasar komoditas',
            ],
        ],
        'Layanan Publik' => [
            'pengguna' => 'warga dan petugas kelurahan',
            'tujuan' => [
                'menjawab pertanyaan seputar persyaratan dokumen kependudukan',
                'mengklasifikasikan dan meneruskan aduan warga ke dinas terkait',
                'memantau status permohonan izin secara transparan',
            ],
        ],
        'Software Testing' => [
            'pengguna' => 'tim QA dan developer',
            'tujuan' => [
                'menghasilkan test case end-to-end dari user story',
                'menemukan regresi visual setelah deployment',
                'menganalisis log CI dan mengusulkan perbaikan test yang flaky',
            ],
        ],
        'Keamanan Siber' => [
            'pengguna' => 'tim keamanan TI kampus',
            'tujuan' => [
                'memprioritaskan hasil pemindaian kerentanan',
                'menganalisis log autentikasi untuk mendeteksi brute force',
                'menyusun laporan insiden keamanan secara otomatis',
            ],
        ],
    ];

    /**
     * Jenis agen beserta cara kerjanya.
     *
     * @var array<string, string>
     */
    private const JENIS_AGEN = [
        'Multi-Agent' => 'beberapa agen dengan peran berbeda (perencana, eksekutor, dan peninjau) yang saling berkoordinasi',
        'RAG Agent' => 'agen retrieval-augmented generation yang menjawab berdasarkan basis pengetahuan terkurasi',
        'Tool-Using Agent' => 'agen yang memanggil API, basis data, dan tool eksternal untuk menyelesaikan tugas',
        'Autonomous Planner' => 'agen perencana otonom yang memecah tujuan menjadi langkah-langkah dan mengeksekusinya',
    ];

    /**
     * Provider/framework agen beserta prefix API key dummy-nya.
     *
     * @var array<string, string>
     */
    private const TEMA_AGENT = [
        'Ollama' => 'ollama-',
        'OpenAI' => 'sk-proj-',
        'Claude' => 'sk-ant-',
        'Gemini' => 'AIza',
        'LangChain' => 'lsv2_pt_',
        'CrewAI' => 'crew-',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $domain = fake()->randomElement(array_keys(self::DOMAIN));
        $tujuan = fake()->randomElement(self::DOMAIN[$domain]['tujuan']);
        $pengguna = self::DOMAIN[$domain]['pengguna'];
        $jenisAgen = fake()->randomElement(array_keys(self::JENIS_AGEN));
        $tema = fake()->randomElement(array_keys(self::TEMA_AGENT));
        $dibuat = fake()->dateTimeBetween('-3 months');

        return [
            'judul' => sprintf('%s %s: %s', $jenisAgen, $domain, Str::ucfirst($tujuan)),
            'deskripsi' => sprintf(
                'Sistem %s berbasis %s yang membantu %s %s. Arsitekturnya memakai %s, '
                    .'dengan human-in-the-loop untuk keputusan berisiko. Target evaluasi: %s.',
                $jenisAgen,
                $tema,
                $pengguna,
                $tujuan,
                self::JENIS_AGEN[$jenisAgen],
                fake()->randomElement([
                    'akurasi jawaban minimal 85% pada data uji',
                    'waktu penyelesaian tugas berkurang 40% dibanding proses manual',
                    'tingkat kepuasan pengguna minimal 4 dari 5 pada uji coba',
                    'jumlah kesalahan manusia turun setidaknya 30%',
                ]),
            ),
            'tema_agent' => $tema,
            // API key dummy berformat mirip asli; disimpan terenkripsi oleh cast model.
            'api_key_secure' => self::TEMA_AGENT[$tema].Str::random(32),
            // Tanggal bervariasi agar urutan "terbaru" di dashboard bermakna.
            'created_at' => $dibuat,
            'updated_at' => $dibuat,
        ];
    }
}
