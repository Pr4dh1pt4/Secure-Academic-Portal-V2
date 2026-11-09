<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\ToolExecutionLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Factory log pemanggilan tool oleh agent AI.
 *
 * Nama tool, input, dan output menyesuaikan jenis agent (loggable_type),
 * sehingga SecurityScanner tidak pernah tercatat memanggil tool milik MalwareScanner.
 *
 * @extends Factory<ToolExecutionLog>
 */
class ToolExecutionLogFactory extends Factory
{
    /**
     * Tool yang tersedia untuk setiap jenis agent (alias morph map).
     *
     * @var array<string, list<string>>
     */
    private const TOOLS = [
        'security_scanner' => ['scan_owasp_top10', 'check_security_headers', 'audit_dependencies'],
        'malware_scanner' => ['scan_uploaded_file', 'check_file_hash', 'scan_directory'],
        'log_analyzer' => ['detect_bruteforce', 'summarize_error_log', 'search_log_pattern'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $alias = fake()->randomElement(array_keys(self::TOOLS));

        return [
            'chat_message_id' => ChatMessage::factory()->fromAssistant(),
            'loggable_type' => $alias,
            'loggable_id' => Relation::getMorphedModel($alias)::factory(),
            // Closure dievaluasi setelah relasi for() diterapkan, jadi tool selalu cocok dengan agent-nya.
            'tool_name' => fn (array $atribut) => fake()->randomElement(self::TOOLS[$atribut['loggable_type']]),
            'input' => fn (array $atribut) => $this->input($atribut['tool_name']),
            'output' => fn (array $atribut) => $this->output($atribut['tool_name']),
            'status' => fake()->randomElement(['berhasil', 'berhasil', 'berhasil', 'berhasil', 'gagal', 'timeout']),
            'durasi_ms' => fake()->numberBetween(120, 9000),
        ];
    }

    /**
     * Argumen pemanggilan tool.
     *
     * @return array<string, mixed>
     */
    private function input(string $tool): array
    {
        return match ($tool) {
            'scan_owasp_top10', 'check_security_headers' => ['url' => 'https://portal.its.test'],
            'audit_dependencies' => ['manifest' => fake()->randomElement(['composer.lock', 'package-lock.json'])],
            'scan_uploaded_file' => ['path' => 'storage/app/uploads/tugas_pbkk.zip'],
            'check_file_hash' => ['sha256' => fake()->sha256()],
            'scan_directory' => ['path' => 'storage/app/uploads', 'recursive' => true],
            'detect_bruteforce' => ['endpoint' => '/login', 'ambang_gagal' => 50, 'jendela_menit' => 10],
            'summarize_error_log' => ['sumber' => 'storage/logs/laravel.log', 'level' => 'error'],
            'search_log_pattern' => ['pola' => fake()->sha256(), 'hari' => 7],
            default => [],
        };
    }

    /**
     * Hasil eksekusi tool.
     *
     * @return array<string, mixed>
     */
    private function output(string $tool): array
    {
        return match ($tool) {
            'scan_owasp_top10' => ['temuan' => fake()->numberBetween(0, 4), 'tingkat_tertinggi' => 'sedang'],
            'check_security_headers' => ['hilang' => fake()->randomElements(['Content-Security-Policy', 'Permissions-Policy'], 1)],
            'audit_dependencies' => ['cve' => fake()->numberBetween(0, 3)],
            'scan_uploaded_file', 'scan_directory' => [
                'dipindai' => fake()->numberBetween(10, 400),
                'terinfeksi' => fake()->numberBetween(0, 1),
            ],
            'check_file_hash' => ['dikenal_berbahaya' => fake()->boolean()],
            'detect_bruteforce' => ['ip_mencurigakan' => fake()->numberBetween(0, 3)],
            'summarize_error_log' => ['total_error' => fake()->numberBetween(0, 500)],
            'search_log_pattern' => ['kecocokan' => fake()->numberBetween(0, 5)],
            default => [],
        };
    }
}
