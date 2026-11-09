<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProposalRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Kelola proposal Agentic AI seluruh mahasiswa.
 *
 * Admin dan Staf boleh melihat, menambah, dan mengubah. Menghapus hanya Admin.
 */
class ProposalController extends Controller implements HasMiddleware
{
    /**
     * Pengaman server-side per aksi, sehingga URL tidak bisa ditembak langsung
     * walaupun tombolnya disembunyikan di Blade.
     *
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:proposal.lihat', only: ['index']),
            new Middleware('permission:proposal.tambah', only: ['create', 'store']),
            new Middleware('permission:proposal.ubah', only: ['edit', 'update']),
            // Hapus data: wajib berperan Admin DAN memegang permission data.hapus.
            new Middleware(['role:Admin', 'permission:data.hapus'], only: ['destroy']),
        ];
    }

    /**
     * Tabel proposal dengan pencarian dan filter.
     *
     * Kueri kondisional when() hanya menambahkan WHERE bila parameternya diisi,
     * dan withQueryString() menjaga ?cari=&tema= tetap ada di link pagination.
     */
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'tema' => ['nullable', Rule::in(StoreProjectRequest::TEMA_AGENT)],
        ]);

        $proposals = Project::query()
            ->with('user:id,name,nrp')
            ->when($filter['cari'] ?? null, function (Builder $query, string $cari) {
                $query->where(function (Builder $query) use ($cari) {
                    $query->where('judul', 'like', "%{$cari}%")
                        ->orWhere('deskripsi', 'like', "%{$cari}%")
                        ->orWhereHas('user', fn (Builder $user) => $user
                            ->where('name', 'like', "%{$cari}%")
                            ->orWhere('nrp', 'like', "%{$cari}%"));
                });
            })
            ->when($filter['tema'] ?? null, fn (Builder $query, string $tema) => $query->where('tema_agent', $tema))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.proposal.index', [
            'proposals' => $proposals,
            'temaAgent' => StoreProjectRequest::TEMA_AGENT,
        ]);
    }

    /**
     * Formulir tambah proposal.
     */
    public function create(): View
    {
        return view('admin.proposal.create', [
            'proposal' => new Project,
            'mahasiswa' => User::mahasiswa()->orderBy('name')->get(['id', 'name', 'nrp']),
            'temaAgent' => StoreProjectRequest::TEMA_AGENT,
        ]);
    }

    /**
     * Simpan proposal baru milik mahasiswa yang dipilih.
     */
    public function store(ProposalRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $pemilik = User::mahasiswa()->findOrFail($data['user_id']);

        // user_id tidak ada di $fillable: kepemilikan diisi lewat relasi.
        $proposal = $pemilik->projects()->create(Arr::except($data, 'user_id'));

        return redirect()
            ->route('admin.proposal.index')
            ->with('status', "Proposal \"{$proposal->judul}\" berhasil ditambahkan.");
    }

    /**
     * Formulir ubah proposal.
     */
    public function edit(Project $proposal): View
    {
        return view('admin.proposal.edit', [
            'proposal' => $proposal->load('user:id,name,nrp'),
            'temaAgent' => StoreProjectRequest::TEMA_AGENT,
        ]);
    }

    /**
     * Simpan perubahan proposal. API key kosong berarti key lama dipertahankan.
     */
    public function update(ProposalRequest $request, Project $proposal): RedirectResponse
    {
        $data = $request->validated();

        if (blank($data['api_key_secure'] ?? null)) {
            unset($data['api_key_secure']);
        }

        $proposal->update($data);

        return redirect()
            ->route('admin.proposal.index')
            ->with('status', "Proposal \"{$proposal->judul}\" berhasil diperbarui.");
    }

    /**
     * Hapus proposal (khusus Admin).
     */
    public function destroy(Project $proposal): RedirectResponse
    {
        $proposal->delete();

        return back()->with('status', "Proposal \"{$proposal->judul}\" berhasil dihapus.");
    }
}
