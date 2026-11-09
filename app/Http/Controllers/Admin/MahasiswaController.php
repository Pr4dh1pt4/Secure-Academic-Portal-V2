<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/**
 * Data mahasiswa (akun @student.its.ac.id).
 */
class MahasiswaController extends Controller implements HasMiddleware
{
    /**
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:mahasiswa.lihat', only: ['index']),
            new Middleware(['role:Admin', 'permission:data.hapus'], only: ['destroy']),
        ];
    }

    /**
     * Tabel mahasiswa dengan pencarian (nama/NRP/email) dan filter program studi.
     */
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'prodi' => ['nullable', 'string', 'max:100'],
        ]);

        $mahasiswa = User::mahasiswa()
            ->withCount(['projects', 'assignments'])
            ->when($filter['cari'] ?? null, function (Builder $query, string $cari) {
                $query->where(fn (Builder $query) => $query
                    ->where('name', 'like', "%{$cari}%")
                    ->orWhere('nrp', 'like', "%{$cari}%")
                    ->orWhere('email', 'like', "%{$cari}%"));
            })
            ->when($filter['prodi'] ?? null, fn (Builder $query, string $prodi) => $query->where('program_studi', $prodi))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.mahasiswa.index', [
            'mahasiswa' => $mahasiswa,
            'daftarProdi' => User::mahasiswa()
                ->whereNotNull('program_studi')
                ->distinct()
                ->orderBy('program_studi')
                ->pluck('program_studi'),
        ]);
    }

    /**
     * Hapus akun mahasiswa beserta proposal, tugas, dan obrolannya (cascade). Khusus Admin.
     */
    public function destroy(Request $request, User $mahasiswa): RedirectResponse
    {
        // Rute ini hanya untuk akun mahasiswa; akun staf/admin dikelola di menu Peran Pengguna.
        abort_unless($mahasiswa->isMahasiswa(), 404);
        abort_if($mahasiswa->is($request->user()) || $mahasiswa->bisaMasukBackoffice(), 403, 'Akun pengelola tidak bisa dihapus dari menu ini.');

        $mahasiswa->delete();

        return back()->with('status', "Akun mahasiswa {$mahasiswa->name} beserta datanya berhasil dihapus.");
    }
}
