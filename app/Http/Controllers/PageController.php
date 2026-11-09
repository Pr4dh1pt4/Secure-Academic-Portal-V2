<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman portofolio publik. Seluruh data (profil, proposal, riwayat tugas)
 * dibaca dari database milik akun pemilik portofolio, bukan array statis.
 */
class PageController extends Controller
{
    /**
     * Beranda. Tantangan 2: query ?user=Nama → sambutan dinamis.
     */
    public function beranda(Request $request): View
    {
        $user = trim((string) $request->query('user', ''));
        $owner = $this->owner()->loadCount(['projects', 'assignments']);

        return view('pages.beranda', [
            'owner' => $owner,
            'user' => $user !== '' ? $user : null,
        ]);
    }

    /**
     * Profil mahasiswa beserta riwayat tugas terbaru.
     */
    public function profil(): View
    {
        $owner = $this->owner();

        return view('pages.profil', [
            'owner' => $owner,
            'assignments' => $owner->assignments()->latest('dikumpulkan_pada')->get(),
        ]);
    }

    /**
     * Ide-Riset: proposal unggulan (yang punya tahapan alur kerja) beserta proposal lain dan formulir ide.
     * Tantangan 1: query ?mode=dark → tema gelap.
     */
    public function ideAgent(Request $request): View
    {
        $owner = $this->owner();
        $projects = $owner->projects()->latest()->get();
        $unggulan = $projects->firstWhere(fn ($project) => ! empty($project->tahapan)) ?? $projects->first();

        return view('pages.ide-agent', [
            'owner' => $owner,
            'isDark' => $request->query('mode') === 'dark',
            'unggulan' => $unggulan,
            'lainnya' => $projects->reject(fn ($project) => $project->is($unggulan)),
            'temaAgent' => StoreProjectRequest::TEMA_AGENT,
        ]);
    }

    /**
     * Simpan proposal ide dari formulir ke database sebagai milik user yang login.
     *
     * Dibuat lewat relasi sehingga user_id tidak bisa dimanipulasi dari request.
     */
    public function submitIde(StoreProjectRequest $request): RedirectResponse
    {
        $project = $request->user()->projects()->create($request->validated());

        return redirect()
            ->route('dashboard')
            ->with('status', 'Ide "'.$project->judul.'" berhasil disimpan ke portofolio Anda.');
    }

    /**
     * Akun pemilik portofolio publik (lihat config/portfolio.php).
     */
    private function owner(): User
    {
        return User::where('email', config('portfolio.owner_email'))->firstOrFail();
    }
}
