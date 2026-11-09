<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memastikan dashboard hanya menampilkan data milik user yang login.
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_only_shows_own_projects_without_plain_api_key(): void
    {
        $user = User::factory()->has(Project::factory()->count(2))->create();
        $other = User::factory()->has(Project::factory())->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        foreach ($user->projects as $project) {
            $response->assertSee($project->judul);
            $response->assertDontSee($project->api_key_secure);
        }
        $response->assertDontSee($other->projects->first()->judul);
    }

    public function test_dashboard_only_shows_own_assignments(): void
    {
        $user = User::factory()->create();
        $user->assignments()->create(Assignment::factory()->raw(['judul' => 'Tugas Milik Saya']));
        $other = User::factory()->create();
        $other->assignments()->create(Assignment::factory()->raw(['judul' => 'Tugas Orang Lain']));

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Tugas Milik Saya')
            ->assertDontSee('Tugas Orang Lain');
    }

    public function test_deleting_user_cascades_to_projects_and_assignments(): void
    {
        $user = User::factory()
            ->has(Project::factory()->count(2))
            ->has(Assignment::factory()->count(2))
            ->create();

        $user->delete();

        $this->assertDatabaseCount('projects', 0);
        $this->assertDatabaseCount('assignments', 0);
    }
}
