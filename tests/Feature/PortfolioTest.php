<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoUserSeeder;
use Database\Seeders\PortfolioOwnerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Halaman portofolio publik membaca dari database, dan formulir ide menyimpan dengan aman.
 */
class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PortfolioOwnerSeeder::class);
    }

    private function owner(): User
    {
        return User::where('email', config('portfolio.owner_email'))->firstOrFail();
    }

    public function test_public_pages_follow_database_changes(): void
    {
        $this->owner()->update(['name' => 'Nama Dari Database', 'keahlian' => ['Rust']]);
        $this->owner()->projects()->where('judul', 'AutoQA Agent')->update(['judul' => 'AutoQA Agent v2']);

        $this->get('/portofolio')->assertOk()->assertSee('Nama Dari Database');
        $this->get('/profil-mahasiswa')->assertOk()->assertSee('Rust')->assertSee('Tugas 4: Aplikasi Multi-View Profil Akademik');
        $this->get('/ide-agent')->assertOk()->assertSee('AutoQA Agent v2')->assertSee('Verified Pull Request');
    }

    public function test_portfolio_link_only_shown_to_portfolio_owner(): void
    {
        $this->seed(DemoUserSeeder::class);
        $dosen = User::where('email', 'dosenpbkk@its.ac.id')->firstOrFail();
        $mahasiswaLain = User::factory()->create();

        $this->actingAs($dosen)->get('/dashboard')->assertOk()->assertDontSee('Portofolio Publik');
        $this->actingAs($mahasiswaLain)->get('/dashboard')->assertOk()->assertDontSee('Portofolio Publik');
        $this->actingAs($this->owner())->get('/dashboard')->assertOk()->assertSee('Portofolio Publik Saya');
    }

    public function test_guest_cannot_submit_idea(): void
    {
        $this->post('/ide-agent', ['judul' => 'Ide tamu'])->assertRedirect('/login');

        $this->assertDatabaseMissing('projects', ['judul' => 'Ide tamu']);
    }

    public function test_submitted_idea_belongs_to_logged_in_user_even_if_user_id_is_injected(): void
    {
        $user = User::factory()->create();
        $korban = $this->owner();

        $this->actingAs($user)->post('/ide-agent', [
            'judul' => 'Agen Penjadwal Praktikum',
            'tema_agent' => 'Gemini',
            'api_key_secure' => 'AIza-rahasia-12345678',
            'user_id' => $korban->id, // Percobaan mass assignment ke akun lain.
        ])->assertRedirect('/dashboard');

        $project = $user->projects()->where('judul', 'Agen Penjadwal Praktikum')->firstOrFail();
        $this->assertSame($user->id, $project->user_id);
        $this->assertSame('AIza-rahasia-12345678', $project->api_key_secure);
        $this->assertNotSame('AIza-rahasia-12345678', DB::table('projects')->where('id', $project->id)->value('api_key_secure'));
    }

    public function test_invalid_tema_agent_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/ide-agent', ['judul' => 'Ide', 'tema_agent' => 'Skynet', 'api_key_secure' => 'abcdefgh'])
            ->assertSessionHasErrors('tema_agent');
    }
}
