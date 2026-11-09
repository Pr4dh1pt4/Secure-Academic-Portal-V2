<?php

namespace Database\Seeders;

use App\Models\ChatSession;
use App\Models\LogAnalyzer;
use App\Models\MalwareScanner;
use App\Models\SecurityScanner;
use App\Models\User;
use Database\Factories\ChatSessionFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder riwayat obrolan Agentic AI: 3 agent keamanan (satu per jenis), lalu
 * setiap user mendapat 2 sesi obrolan berisi 6 pesan beserta log tool-nya.
 *
 * Aman dijalankan berulang kali: agent di-upsert, dan user yang sudah punya sesi dilewati.
 */
class ChatSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Jumlah sesi obrolan per user.
     */
    private const SESI_PER_USER = 2;

    /**
     * Seed agent dan riwayat obrolan.
     */
    public function run(): void
    {
        SecurityScanner::updateOrCreate(['nama' => 'WebSentinel'], [
            'versi' => '2.4.1',
            'target_url' => 'https://portal.its.test',
            'standar' => 'OWASP Top 10',
        ]);
        MalwareScanner::updateOrCreate(['nama' => 'FileGuard'], [
            'versi' => '1.8.0',
            'engine' => 'ClamAV',
            'signature_diperbarui_pada' => now()->toDateString(),
        ]);
        LogAnalyzer::updateOrCreate(['nama' => 'LogLens'], [
            'versi' => '3.0.2',
            'sumber_log' => 'storage/logs/laravel.log',
            'jendela_jam' => 24,
        ]);

        $judul = array_column(ChatSessionFactory::SKENARIO, 'judul');

        User::doesntHave('chatSessions')->each(function (User $user) use ($judul) {
            // Dua skenario berbeda per user, bergantian agar semua skenario terpakai.
            for ($i = 0; $i < self::SESI_PER_USER; $i++) {
                $dibuat = fake()->dateTimeBetween('-30 days', '-1 day');

                ChatSession::factory()
                    ->for($user)
                    ->withConversation()
                    ->create([
                        'judul' => $judul[($user->id + $i) % count($judul)],
                        'created_at' => $dibuat,
                        'updated_at' => $dibuat,
                    ]);
            }
        });
    }
}
