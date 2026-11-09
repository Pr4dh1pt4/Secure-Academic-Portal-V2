<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Pengaturan peran pengguna (khusus Admin). Peran disimpan ke tabel model_has_roles milik Spatie.
 */
class PenggunaController extends Controller implements HasMiddleware
{
    /**
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware(['role:Admin', 'permission:pengguna.kelola']),
        ];
    }

    /**
     * Daftar pengguna beserta perannya. Filter "peran" memakai whereHas lewat scope role() Spatie.
     */
    public function index(Request $request): View
    {
        $daftarPeran = Role::query()->orderBy('name')->pluck('name');

        $filter = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'peran' => ['nullable', Rule::in([...$daftarPeran, 'tanpa'])],
        ]);

        $pengguna = User::query()
            ->with('roles:id,name')
            ->when($filter['cari'] ?? null, fn (Builder $query, string $cari) => $query->where(fn (Builder $query) => $query
                ->where('name', 'like', "%{$cari}%")
                ->orWhere('email', 'like', "%{$cari}%")))
            ->when($filter['peran'] ?? null, fn (Builder $query, string $peran) => $peran === 'tanpa'
                ? $query->doesntHave('roles')
                : $query->role($peran))
            // Pengelola (punya peran) tampil lebih dulu.
            ->withCount('roles')
            ->orderByDesc('roles_count')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengguna.index', [
            'pengguna' => $pengguna,
            'daftarPeran' => $daftarPeran,
        ]);
    }

    /**
     * Ganti peran seorang pengguna. Nilai kosong = cabut semua peran.
     */
    public function update(Request $request, User $pengguna): RedirectResponse
    {
        $data = $request->validate([
            'peran' => ['nullable', Rule::exists(Role::class, 'name')],
        ]);

        // Mencegah Admin mengunci dirinya sendiri keluar dari panel.
        if ($pengguna->is($request->user())) {
            return back()->withErrors(['peran' => 'Anda tidak bisa mengubah peran akun Anda sendiri.']);
        }

        $pengguna->syncRoles(array_filter([$data['peran'] ?? null]));

        $label = $data['peran'] ?? 'tanpa peran';

        return back()->with('status', "Peran {$pengguna->name} sekarang: {$label}.");
    }
}
