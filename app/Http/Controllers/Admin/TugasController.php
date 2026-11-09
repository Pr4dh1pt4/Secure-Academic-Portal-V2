<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Riwayat tugas kuliah seluruh mahasiswa.
 */
class TugasController extends Controller implements HasMiddleware
{
    /**
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:tugas.lihat', only: ['index']),
            new Middleware(['role:Admin', 'permission:data.hapus'], only: ['destroy']),
        ];
    }

    /**
     * Tabel tugas dengan pencarian, filter mata kuliah, dan filter status penilaian.
     */
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'mata_kuliah' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['dinilai', 'belum'])],
        ]);

        $tugas = Assignment::query()
            ->with('user:id,name,nrp')
            ->when($filter['cari'] ?? null, function (Builder $query, string $cari) {
                $query->where(fn (Builder $query) => $query
                    ->where('judul', 'like', "%{$cari}%")
                    ->orWhereHas('user', fn (Builder $user) => $user
                        ->where('name', 'like', "%{$cari}%")
                        ->orWhere('nrp', 'like', "%{$cari}%")));
            })
            ->when($filter['mata_kuliah'] ?? null, fn (Builder $query, string $mk) => $query->where('mata_kuliah', $mk))
            ->when($filter['status'] ?? null, fn (Builder $query, string $status) => $status === 'dinilai'
                ? $query->whereNotNull('nilai')
                : $query->whereNull('nilai'))
            ->latest('dikumpulkan_pada')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.tugas.index', [
            'tugas' => $tugas,
            'daftarMataKuliah' => Assignment::query()->distinct()->orderBy('mata_kuliah')->pluck('mata_kuliah'),
        ]);
    }

    /**
     * Hapus riwayat tugas (khusus Admin).
     */
    public function destroy(Assignment $tugas): RedirectResponse
    {
        $tugas->delete();

        return back()->with('status', "Tugas \"{$tugas->judul}\" berhasil dihapus.");
    }
}
