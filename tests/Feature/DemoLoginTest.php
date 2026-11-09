<?php

namespace Tests\Feature;

use Database\Seeders\DemoUserSeeder;
use Database\Seeders\PortfolioOwnerSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tombol login instan akun uji di halaman login.
 */
class DemoLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([DemoUserSeeder::class, PortfolioOwnerSeeder::class]);
    }

    public function test_buttons_are_shown_when_enabled(): void
    {
        config(['demo.enabled' => true]);

        $this->get('/login')
            ->assertOk()
            ->assertSee('Masuk sebagai Admin')
            ->assertSee('Masuk sebagai Staf')
            ->assertSee('Masuk sebagai Mahasiswa');
    }

    public function test_each_demo_account_logs_in_to_its_home_page(): void
    {
        config(['demo.enabled' => true]);

        $tujuan = ['admin' => '/admin', 'staf' => '/admin', 'mahasiswa' => '/dashboard'];

        foreach ($tujuan as $akun => $halaman) {
            $this->post("/login/demo/{$akun}")->assertRedirect($halaman);
            $this->assertAuthenticated();
            $this->assertSame(config("demo.accounts.{$akun}.email"), auth()->user()->email);

            $this->post('/logout');
        }
    }

    public function test_disabled_feature_hides_buttons_and_rejects_requests(): void
    {
        config(['demo.enabled' => false]);

        $this->get('/login')->assertOk()->assertDontSee('Masuk sebagai Admin');
        $this->post('/login/demo/admin')->assertNotFound();
        $this->assertGuest();
    }

    public function test_never_active_in_production(): void
    {
        config(['demo.enabled' => true]);
        $this->app->detectEnvironment(fn () => 'production');
        // Di luar environment testing CSRF ikut aktif (419); dimatikan agar yang diuji adalah pengaman controller.
        $this->withoutMiddleware(PreventRequestForgery::class);

        $this->get('/login')->assertDontSee('Masuk sebagai Admin');
        $this->post('/login/demo/admin')->assertNotFound();
        $this->assertGuest();
    }

    public function test_unknown_account_is_rejected(): void
    {
        config(['demo.enabled' => true]);

        $this->post('/login/demo/root')->assertNotFound();
        $this->assertGuest();
    }
}
