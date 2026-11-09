<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\LogAnalyzer;
use App\Models\MalwareScanner;
use App\Models\SecurityScanner;
use App\Models\ToolExecutionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Relasi User -> ChatSession -> ChatMessage -> ToolExecutionLog (+ polymorphic loggable),
 * cascade delete, isolasi data, dan bebas N+1 di halaman riwayat obrolan.
 */
class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_relations_follow_the_spec(): void
    {
        $scanner = SecurityScanner::factory()->create();
        $log = ToolExecutionLog::factory()->for($scanner, 'loggable')->create();

        $message = $log->chatMessage;
        $session = $message->chatSession;

        $this->assertTrue($session->user->chatSessions->contains($session));
        $this->assertTrue($session->messages->contains($message));
        $this->assertTrue($message->toolExecutionLogs->contains($log));
        $this->assertTrue($session->toolExecutionLogs->contains($log));
    }

    public function test_tool_log_is_polymorphic_across_agent_types(): void
    {
        $message = ChatMessage::factory()->fromAssistant()->create();

        foreach ([SecurityScanner::class, MalwareScanner::class, LogAnalyzer::class] as $agentClass) {
            $agent = $agentClass::factory()->create();
            ToolExecutionLog::factory()->for($message)->for($agent, 'loggable')->create();

            $this->assertCount(1, $agent->toolExecutionLogs);
            $this->assertTrue($agent->toolExecutionLogs->first()->loggable->is($agent));
        }

        // loggable_type menyimpan alias morph map, bukan nama class lengkap.
        $this->assertEqualsCanonicalizing(
            ['security_scanner', 'malware_scanner', 'log_analyzer'],
            ToolExecutionLog::pluck('loggable_type')->all(),
        );
    }

    public function test_deleting_user_cascades_to_sessions_messages_and_logs(): void
    {
        $session = ChatSession::factory()->withConversation()->create();

        $this->assertGreaterThan(0, ToolExecutionLog::count());

        $session->user->delete();

        $this->assertDatabaseCount('chat_sessions', 0);
        $this->assertDatabaseCount('chat_messages', 0);
        $this->assertDatabaseCount('tool_execution_logs', 0);
    }

    public function test_deleting_agent_removes_its_logs(): void
    {
        $scanner = MalwareScanner::factory()->create();
        ToolExecutionLog::factory()->count(2)->for($scanner, 'loggable')->create();
        ToolExecutionLog::factory()->for(LogAnalyzer::factory(), 'loggable')->create();

        $scanner->delete();

        $this->assertDatabaseCount('tool_execution_logs', 1);
    }

    public function test_user_only_sees_own_sessions(): void
    {
        $user = User::factory()->create();
        $own = ChatSession::factory()->for($user)->create(['judul' => 'Sesi Milik Saya']);
        $other = ChatSession::factory()->create(['judul' => 'Sesi Orang Lain']);

        $this->actingAs($user)->get('/chat')
            ->assertOk()
            ->assertSee('Sesi Milik Saya')
            ->assertDontSee('Sesi Orang Lain');

        $this->actingAs($user)->get("/chat/{$own->id}")->assertOk();
        $this->actingAs($user)->get("/chat/{$other->id}")->assertNotFound();
    }

    public function test_chat_detail_query_count_does_not_grow_with_messages(): void
    {
        // Relasi polymorphic butuh satu query per jenis agent, jadi kedua sesi memakai skenario
        // yang memanggil ketiga jenis agent agar jumlah query-nya sebanding.
        $skenario = ['judul' => 'Review keamanan sebelum demo proyek Agentic AI'];
        $user = User::factory()->create();
        $kecil = ChatSession::factory()->for($user)->withConversation()->create($skenario);
        $besar = ChatSession::factory()->for($user)->withConversation()->create($skenario);

        // Sesi besar mendapat 20 pesan tambahan, masing-masing dengan 3 log dari agent acak.
        ChatMessage::factory()->count(20)->fromAssistant()->for($besar)
            ->has(ToolExecutionLog::factory()->count(3))
            ->create();

        // fresh(): setiap request memakai instance user baru (seperti request asli), sehingga query
        // peran Spatie untuk menu navigasi ikut terhitung di kedua request, bukan hanya yang pertama.
        $kecilQueries = $this->countQueries(fn () => $this->actingAs($user->fresh())->get("/chat/{$kecil->id}")->assertOk());
        $besarQueries = $this->countQueries(fn () => $this->actingAs($user->fresh())->get("/chat/{$besar->id}")->assertOk());

        // Jumlah query sama walau pesan dan log jauh lebih banyak: bebas N+1.
        $this->assertSame($kecilQueries, $besarQueries);
        $this->assertLessThanOrEqual(8, $besarQueries);
    }

    /**
     * Hitung jumlah query SQL yang dijalankan selama callback.
     */
    private function countQueries(callable $callback): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $callback();
        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    }
}
