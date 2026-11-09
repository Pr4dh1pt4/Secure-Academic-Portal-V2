<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\ChatSession;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Panel backoffice AdminLTE + RBAC Spatie: akses per peran, hak eksklusif hapus data,
 * widget statistik, pencarian when(), dan pagination withQueryString().
 */
class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function pengguna(?string $peran = null, array $atribut = []): User
    {
        $user = User::factory()->create($atribut);

        if ($peran !== null) {
            $user->assignRole($peran);
        }

        return $user;
    }

    // ---------------------------------------------------------------- Akses panel

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_unverified_admin_is_redirected_to_verification_notice(): void
    {
        $admin = User::factory()->unverified()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin)->get('/admin')->assertRedirect(route('verification.notice'));
    }

    public function test_user_without_role_cannot_open_panel(): void
    {
        $mahasiswa = $this->pengguna();

        $this->actingAs($mahasiswa)->get('/admin')->assertForbidden();
        $this->actingAs($mahasiswa)->get('/admin/proposal')->assertForbidden();
    }

    public function test_admin_and_staf_land_on_panel_after_login(): void
    {
        foreach (['Admin', 'Staf'] as $peran) {
            $user = $this->pengguna($peran);

            $this->post('/login', ['email' => $user->email, 'password' => 'password'])
                ->assertRedirect('/admin');

            $this->post('/logout');
        }

        $mahasiswa = $this->pengguna();
        $this->post('/login', ['email' => $mahasiswa->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard', absolute: false));
    }

    // ---------------------------------------------------------------- Widget dashboard

    public function test_dashboard_shows_aggregate_widgets(): void
    {
        $mahasiswa = User::factory(3)->has(Project::factory()->count(2))->has(Assignment::factory()->count(1))->create();
        ChatSession::factory()->for($mahasiswa->first())->create();

        $response = $this->actingAs($this->pengguna('Staf', ['email' => 'staf@its.ac.id']))->get('/admin');

        $response->assertOk()
            ->assertSee('adminlte', false)
            ->assertSeeInOrder(['Mahasiswa', 'Proposal Agentic AI', 'Riwayat Tugas', 'Sesi Obrolan AI']);

        // Nilai widget berasal dari count() di database.
        $widgets = collect($response->viewData('widgets'))->pluck('total', 'judul');
        $this->assertSame(3, $widgets['Mahasiswa']);
        $this->assertSame(6, $widgets['Proposal Agentic AI']);
        $this->assertSame(3, $widgets['Riwayat Tugas']);
        $this->assertSame(1, $widgets['Sesi Obrolan AI']);
    }

    // ---------------------------------------------------------------- Hak eksklusif hapus data

    public function test_delete_button_only_rendered_for_admin(): void
    {
        Project::factory()->for(User::factory())->create();
        Assignment::factory()->for(User::factory())->create();

        $admin = $this->pengguna('Admin', ['email' => 'admin@its.ac.id']);
        $staf = $this->pengguna('Staf', ['email' => 'staf@its.ac.id']);

        foreach (['/admin/proposal', '/admin/mahasiswa', '/admin/tugas'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk()->assertSee('data-testid="tombol-hapus"', false);
            $this->actingAs($staf)->get($url)->assertOk()->assertDontSee('data-testid="tombol-hapus"', false);
        }
    }

    public function test_staf_cannot_delete_by_hitting_url_directly(): void
    {
        $proposal = Project::factory()->for(User::factory())->create();
        $tugas = Assignment::factory()->for(User::factory())->create();
        $mahasiswa = User::factory()->create();
        $staf = $this->pengguna('Staf', ['email' => 'staf@its.ac.id']);

        $this->actingAs($staf)->delete("/admin/proposal/{$proposal->id}")->assertForbidden();
        $this->actingAs($staf)->delete("/admin/tugas/{$tugas->id}")->assertForbidden();
        $this->actingAs($staf)->delete("/admin/mahasiswa/{$mahasiswa->id}")->assertForbidden();

        $this->assertModelExists($proposal);
        $this->assertModelExists($tugas);
        $this->assertModelExists($mahasiswa);
    }

    public function test_permission_alone_is_not_enough_without_admin_role(): void
    {
        // Walaupun permission data.hapus diberikan langsung, Staf tetap ditolak: hapus wajib peran Admin murni.
        $staf = $this->pengguna('Staf', ['email' => 'staf@its.ac.id']);
        $staf->givePermissionTo('data.hapus');
        $proposal = Project::factory()->for(User::factory())->create();

        $this->actingAs($staf)->get('/admin/proposal')->assertDontSee('data-testid="tombol-hapus"', false);
        $this->actingAs($staf)->delete("/admin/proposal/{$proposal->id}")->assertForbidden();
        $this->assertModelExists($proposal);
    }

    public function test_admin_can_delete_data(): void
    {
        $admin = $this->pengguna('Admin', ['email' => 'admin@its.ac.id']);
        $proposal = Project::factory()->for(User::factory())->create();
        $tugas = Assignment::factory()->for(User::factory())->create();
        $mahasiswa = User::factory()->has(Project::factory())->create();

        $this->actingAs($admin)->delete("/admin/proposal/{$proposal->id}")->assertRedirect();
        $this->actingAs($admin)->delete("/admin/tugas/{$tugas->id}")->assertRedirect();
        $this->actingAs($admin)->delete("/admin/mahasiswa/{$mahasiswa->id}")->assertRedirect();

        $this->assertModelMissing($proposal);
        $this->assertModelMissing($tugas);
        $this->assertModelMissing($mahasiswa);
        $this->assertSame(0, Project::where('user_id', $mahasiswa->id)->count());
    }

    public function test_mahasiswa_route_cannot_delete_staff_accounts(): void
    {
        $admin = $this->pengguna('Admin', ['email' => 'admin@its.ac.id']);
        $staf = $this->pengguna('Staf', ['email' => 'staf@its.ac.id']);

        $this->actingAs($admin)->delete("/admin/mahasiswa/{$staf->id}")->assertNotFound();
        $this->assertModelExists($staf);
    }

    // ---------------------------------------------------------------- Tambah & ubah proposal

    public function test_staf_can_create_proposal_for_a_student(): void
    {
        $staf = $this->pengguna('Staf', ['email' => 'staf@its.ac.id']);
        $mahasiswa = User::factory()->create();

        $this->actingAs($staf)->get('/admin/proposal/create')->assertOk();

        $this->actingAs($staf)->post('/admin/proposal', [
            'user_id' => $mahasiswa->id,
            'judul' => 'Agen Penjadwal Asistensi Praktikum',
            'deskripsi' => 'Menyusun jadwal asistensi otomatis.',
            'tema_agent' => 'Claude',
            'api_key_secure' => 'sk-ant-rahasia-12345678',
        ])->assertRedirect(route('admin.proposal.index'));

        $proposal = Project::where('judul', 'Agen Penjadwal Asistensi Praktikum')->firstOrFail();
        $this->assertSame($mahasiswa->id, $proposal->user_id);
        $this->assertSame('sk-ant-rahasia-12345678', $proposal->api_key_secure);
    }

    public function test_proposal_owner_must_be_a_student(): void
    {
        $staf = $this->pengguna('Staf', ['email' => 'staf@its.ac.id']);

        $this->actingAs($staf)->post('/admin/proposal', [
            'user_id' => $staf->id,
            'judul' => 'Proposal Tidak Sah',
            'tema_agent' => 'Claude',
            'api_key_secure' => 'sk-ant-rahasia-12345678',
        ])->assertSessionHasErrors('user_id');

        $this->assertDatabaseMissing('projects', ['judul' => 'Proposal Tidak Sah']);
    }

    public function test_update_keeps_api_key_when_left_blank(): void
    {
        $staf = $this->pengguna('Staf', ['email' => 'staf@its.ac.id']);
        $proposal = Project::factory()->for(User::factory())->create(['api_key_secure' => 'sk-lama-87654321']);

        $this->actingAs($staf)->get("/admin/proposal/{$proposal->id}/edit")->assertOk();

        $this->actingAs($staf)->put("/admin/proposal/{$proposal->id}", [
            'judul' => 'Judul Baru',
            'tema_agent' => 'Gemini',
            'api_key_secure' => '',
        ])->assertRedirect(route('admin.proposal.index'));

        $proposal->refresh();
        $this->assertSame('Judul Baru', $proposal->judul);
        $this->assertSame('Gemini', $proposal->tema_agent);
        $this->assertSame('sk-lama-87654321', $proposal->api_key_secure);
    }

    // ---------------------------------------------------------------- Pencarian when() + withQueryString()

    public function test_search_and_filter_use_conditional_query(): void
    {
        $budi = User::factory()->create(['name' => 'Budi Santoso']);
        Project::factory()->for($budi)->create(['judul' => 'Agen Irigasi Cerdas', 'tema_agent' => 'Ollama']);
        Project::factory()->for($budi)->create(['judul' => 'Agen Triase Puskesmas', 'tema_agent' => 'Claude']);
        Project::factory()->for(User::factory()->create(['name' => 'Sari Dewi']))->create(['judul' => 'Agen Rute Kurir', 'tema_agent' => 'Ollama']);

        $admin = $this->pengguna('Admin', ['email' => 'admin@its.ac.id']);

        // Tanpa filter: semua tampil.
        $this->actingAs($admin)->get('/admin/proposal')
            ->assertSee('Agen Irigasi Cerdas')->assertSee('Agen Triase Puskesmas')->assertSee('Agen Rute Kurir');

        // Kata kunci mencocokkan judul.
        $this->actingAs($admin)->get('/admin/proposal?cari=Irigasi')
            ->assertSee('Agen Irigasi Cerdas')->assertDontSee('Agen Triase Puskesmas')->assertDontSee('Agen Rute Kurir');

        // Kata kunci mencocokkan nama mahasiswa (whereHas), digabung filter tema.
        $this->actingAs($admin)->get('/admin/proposal?cari=Budi&tema=Ollama')
            ->assertSee('Agen Irigasi Cerdas')->assertDontSee('Agen Triase Puskesmas')->assertDontSee('Agen Rute Kurir');

        // Tema yang tidak valid ditolak validasi.
        $this->actingAs($admin)->get('/admin/proposal?tema=Hacker')->assertSessionHasErrors('tema');
    }

    public function test_pagination_links_keep_search_keyword(): void
    {
        Project::factory()->count(15)->for(User::factory())->create(['judul' => 'Agen Laravel Asisten', 'tema_agent' => 'Ollama']);
        Project::factory()->count(5)->for(User::factory())->create(['judul' => 'Agen Lain', 'tema_agent' => 'Claude']);

        $admin = $this->pengguna('Admin', ['email' => 'admin@its.ac.id']);

        $halaman1 = $this->actingAs($admin)->get('/admin/proposal?cari=Laravel&tema=Ollama');
        $halaman1->assertOk()->assertSee('dari 15 proposal');
        // Link ke halaman 2 tetap membawa cari & tema (withQueryString).
        $halaman1->assertSee('cari=Laravel&amp;tema=Ollama&amp;page=2', false);

        $this->actingAs($admin)->get('/admin/proposal?cari=Laravel&tema=Ollama&page=2')
            ->assertOk()
            ->assertSee('Menampilkan 11–15 dari 15 proposal')
            ->assertDontSee('Agen Lain');
    }

    public function test_student_and_assignment_tables_are_searchable(): void
    {
        User::factory()->create(['name' => 'Rina Kartika', 'program_studi' => 'Sistem Informasi']);
        User::factory()->create(['name' => 'Joko Prasetyo', 'program_studi' => 'Teknik Komputer']);
        $staf = $this->pengguna('Staf', ['email' => 'staf@its.ac.id']);

        $this->actingAs($staf)->get('/admin/mahasiswa?cari=Rina')
            ->assertOk()->assertSee('Rina Kartika')->assertDontSee('Joko Prasetyo');
        $this->actingAs($staf)->get('/admin/mahasiswa?prodi=Teknik+Komputer')
            ->assertOk()->assertSee('Joko Prasetyo')->assertDontSee('Rina Kartika');

        Assignment::factory()->for(User::factory())->create(['judul' => 'Tugas Sudah Dinilai', 'nilai' => 90]);
        Assignment::factory()->for(User::factory())->create(['judul' => 'Tugas Menunggu Nilai', 'nilai' => null]);

        $this->actingAs($staf)->get('/admin/tugas?status=belum')
            ->assertOk()->assertSee('Tugas Menunggu Nilai')->assertDontSee('Tugas Sudah Dinilai');
    }

    // ---------------------------------------------------------------- Peran pengguna

    public function test_only_admin_can_manage_roles(): void
    {
        $staf = $this->pengguna('Staf', ['email' => 'staf@its.ac.id']);
        $target = User::factory()->create();

        $this->actingAs($staf)->get('/admin')->assertDontSee('Peran Pengguna');
        $this->actingAs($staf)->get('/admin/pengguna')->assertForbidden();
        $this->actingAs($staf)->patch("/admin/pengguna/{$target->id}/peran", ['peran' => 'Admin'])->assertForbidden();
        $this->assertFalse($target->fresh()->hasRole('Admin'));
    }

    public function test_admin_can_change_roles_but_not_own_role(): void
    {
        $admin = $this->pengguna('Admin', ['email' => 'admin@its.ac.id']);
        $target = User::factory()->create();

        $this->actingAs($admin)->get('/admin/pengguna')->assertOk()->assertSee($target->name);

        $this->actingAs($admin)->patch("/admin/pengguna/{$target->id}/peran", ['peran' => 'Staf'])->assertRedirect();
        $this->assertTrue($target->fresh()->hasRole('Staf'));
        $this->actingAs($target->fresh())->get('/admin')->assertOk();

        $this->actingAs($admin)->patch("/admin/pengguna/{$target->id}/peran", ['peran' => ''])->assertRedirect();
        $this->assertFalse($target->fresh()->hasAnyRole(['Admin', 'Staf']));

        $this->actingAs($admin)->patch("/admin/pengguna/{$admin->id}/peran", ['peran' => ''])->assertSessionHasErrors('peran');
        $this->assertTrue($admin->fresh()->hasRole('Admin'));
    }
}
