<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Login instan ke akun uji untuk demo di kelas.
 *
 * Pengaman:
 * - Hanya aktif bila config('demo.enabled') bernilai true DAN environment bukan production.
 * - Akun dipilih dari whitelist config('demo.accounts') berdasarkan kunci, bukan email dari request.
 * - Tetap memakai POST + CSRF, dan session diregenerasi seperti login biasa.
 */
class DemoLoginController extends Controller
{
    /**
     * Apakah fitur login instan boleh dipakai.
     */
    public static function aktif(): bool
    {
        return config('demo.enabled') && ! app()->isProduction();
    }

    /**
     * Masuk sebagai salah satu akun uji.
     */
    public function store(Request $request, string $akun): RedirectResponse
    {
        abort_unless(self::aktif(), 404);

        $email = config("demo.accounts.{$akun}.email");
        abort_if($email === null, 404);

        $user = User::where('email', $email)->first();

        if ($user === null) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun demo belum ada. Jalankan php artisan migrate:fresh --seed terlebih dahulu.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->to(AuthenticatedSessionController::halamanAwal($user));
    }
}
