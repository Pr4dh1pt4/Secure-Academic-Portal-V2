<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\ToolExecutionLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Factory sesi obrolan dengan agent AI keamanan.
 *
 * State withConversation() mengisi sesi dengan percakapan utuh yang koheren:
 * pertanyaan user, jawaban agent, dan log tool yang dipanggil agent untuk menjawabnya.
 *
 * @extends Factory<ChatSession>
 */
class ChatSessionFactory extends Factory
{
    /**
     * Skenario percakapan. Setiap giliran berisi pertanyaan user, jawaban agent,
     * dan daftar tool yang dipanggil dalam format [alias agent, nama tool].
     *
     * @var list<array{judul: string, giliran: list<array{tanya: string, jawab: string, tools: list<array{string, string}>}>}>
     */
    public const SKENARIO = [
        [
            'judul' => 'Audit keamanan portal akademik setelah deploy',
            'giliran' => [
                [
                    'tanya' => 'Tolong pindai https://portal.its.test untuk kerentanan OWASP Top 10.',
                    'jawab' => 'Pemindaian selesai. Ada 2 temuan tingkat sedang: header Content-Security-Policy belum '
                        .'dipasang dan cookie sesi belum memakai SameSite=Strict. Tidak ada indikasi SQL injection '
                        .'pada form login.',
                    'tools' => [['security_scanner', 'scan_owasp_top10'], ['security_scanner', 'check_security_headers']],
                ],
                [
                    'tanya' => 'Apakah ada percobaan brute force ke halaman login minggu ini?',
                    'jawab' => 'Ada 3 alamat IP yang gagal login lebih dari 50 kali dalam 10 menit. Saya sarankan '
                        .'mengaktifkan throttle login dan memblokir sementara ketiga IP tersebut.',
                    'tools' => [['log_analyzer', 'detect_bruteforce']],
                ],
                [
                    'tanya' => 'Buatkan urutan prioritas perbaikannya.',
                    'jawab' => 'Prioritas: (1) rate limiting pada login, (2) pasang Content-Security-Policy, '
                        .'(3) set SameSite=Strict pada cookie sesi. Estimasi total sekitar 2 jam kerja.',
                    'tools' => [],
                ],
            ],
        ],
        [
            'judul' => 'Pemeriksaan berkas unggahan tugas mahasiswa',
            'giliran' => [
                [
                    'tanya' => 'Ada mahasiswa mengunggah tugas_pbkk.zip, tolong cek apakah aman.',
                    'jawab' => 'Arsip berisi 14 berkas. Berkas vendor/bin/update.exe terdeteksi sebagai '
                        .'Trojan.GenericKD dan sudah dikarantina. Berkas lain bersih.',
                    'tools' => [['malware_scanner', 'scan_uploaded_file'], ['malware_scanner', 'check_file_hash']],
                ],
                [
                    'tanya' => 'Apakah berkas yang sama pernah muncul di unggahan lain?',
                    'jawab' => 'Hash yang sama muncul di 2 unggahan lain dalam 7 hari terakhir dari akun berbeda. '
                        .'Kemungkinan menyebar lewat template tugas yang dibagikan antarmahasiswa.',
                    'tools' => [['log_analyzer', 'search_log_pattern']],
                ],
                [
                    'tanya' => 'Tuliskan rekomendasi untuk dosen pengampu.',
                    'jawab' => 'Rekomendasi: batasi unggahan hanya untuk arsip berisi kode sumber, aktifkan pemindaian '
                        .'otomatis saat unggah, dan minta 3 mahasiswa terkait mengganti template tugasnya.',
                    'tools' => [],
                ],
            ],
        ],
        [
            'judul' => 'Analisis error 500 setelah migrasi database',
            'giliran' => [
                [
                    'tanya' => 'Sejak migrasi kemarin banyak error 500 di /dashboard. Apa penyebabnya?',
                    'jawab' => 'Sebanyak 87% error berasal dari SQLSTATE[42S22]: kolom tahapan tidak ditemukan. '
                        .'Migration add_tahapan_to_projects tampaknya belum dijalankan di server produksi.',
                    'tools' => [['log_analyzer', 'summarize_error_log']],
                ],
                [
                    'tanya' => 'Migration sudah saya jalankan. Apakah endpoint-nya sekarang aman?',
                    'jawab' => 'Error 500 sudah hilang. Pemindaian ulang tidak menemukan kebocoran stack trace '
                        .'dan APP_DEBUG sudah bernilai false.',
                    'tools' => [['security_scanner', 'scan_owasp_top10'], ['log_analyzer', 'summarize_error_log']],
                ],
                [
                    'tanya' => 'Oke, terima kasih.',
                    'jawab' => 'Sama-sama. Saya akan memantau log 24 jam ke depan dan memberi tahu jika error muncul lagi.',
                    'tools' => [],
                ],
            ],
        ],
        [
            'judul' => 'Review keamanan sebelum demo proyek Agentic AI',
            'giliran' => [
                [
                    'tanya' => 'Cek header keamanan dan dependensi aplikasi sebelum demo besok.',
                    'jawab' => 'Header HSTS dan X-Frame-Options sudah terpasang. Satu paket npm memiliki CVE '
                        .'tingkat tinggi; jalankan npm audit fix sebelum demo.',
                    'tools' => [['security_scanner', 'check_security_headers'], ['security_scanner', 'audit_dependencies']],
                ],
                [
                    'tanya' => 'Pindai juga folder storage/app/uploads.',
                    'jawab' => 'Sebanyak 312 berkas dipindai dan tidak ada malware terdeteksi. Signature sudah diperbarui hari ini.',
                    'tools' => [['malware_scanner', 'scan_directory']],
                ],
                [
                    'tanya' => 'Ada aktivitas mencurigakan di log akses?',
                    'jawab' => 'Tidak ada. Lalu lintas normal dengan puncak 120 request per menit saat jam kuliah.',
                    'tools' => [['log_analyzer', 'detect_bruteforce']],
                ],
            ],
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'judul' => fake()->randomElement(self::SKENARIO)['judul'],
            'model_ai' => fake()->randomElement(['claude-opus-5-5', 'claude-sonnet-5-5', 'llama3.1:8b']),
            'status' => fake()->randomElement(['aktif', 'aktif', 'aktif', 'diarsipkan']),
        ];
    }

    /**
     * Isi sesi dengan percakapan dari skenario yang judulnya cocok.
     *
     * Agent pemanggil tool diambil dari agent yang sudah ada di database;
     * bila belum ada, dibuat lewat factory masing-masing.
     */
    public function withConversation(): static
    {
        return $this->afterCreating(function (ChatSession $session) {
            $skenario = collect(self::SKENARIO)->firstWhere('judul', $session->judul)
                ?? fake()->randomElement(self::SKENARIO);
            $waktu = $session->created_at->copy();

            foreach ($skenario['giliran'] as $giliran) {
                ChatMessage::factory()->fromUser($giliran['tanya'])
                    ->for($session)
                    ->create(['created_at' => $waktu, 'updated_at' => $waktu]);

                $waktu = $waktu->copy()->addSeconds(fake()->numberBetween(5, 40));
                $jawaban = ChatMessage::factory()->fromAssistant($giliran['jawab'])
                    ->for($session)
                    ->create(['created_at' => $waktu, 'updated_at' => $waktu]);

                foreach ($giliran['tools'] as [$alias, $tool]) {
                    ToolExecutionLog::factory()
                        ->for($jawaban)
                        ->for($this->agent($alias), 'loggable')
                        ->create(['tool_name' => $tool, 'created_at' => $waktu, 'updated_at' => $waktu]);
                }

                $waktu = $waktu->copy()->addMinutes(fake()->numberBetween(1, 15));
            }

            $session->forceFill(['updated_at' => $waktu])->saveQuietly();
        });
    }

    /**
     * Agent acak dengan alias morph tertentu, mis. "security_scanner".
     */
    private function agent(string $alias): Model
    {
        /** @var class-string<Model> $model */
        $model = Relation::getMorphedModel($alias);

        return $model::query()->inRandomOrder()->first() ?? $model::factory()->create();
    }
}
