@extends('adminlte::page')

@section('title', 'Proposal Agentic AI')

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h3 class="mb-0">Proposal Agentic AI</h3>
        @can('proposal.tambah')
            <a href="{{ route('admin.proposal.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Proposal
            </a>
        @endcan
    </div>
@stop

@section('content')
    @include('admin.partials.flash')

    <div class="card">
        {{-- Form pencarian: method GET, sehingga filter tersimpan di URL dan ikut di link pagination. --}}
        <div class="card-header">
            <form method="GET" action="{{ route('admin.proposal.index') }}" class="row g-2 align-items-center" role="search">
                <div class="col-md-6">
                    <label for="cari" class="visually-hidden">Kata kunci</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="cari" name="cari" value="{{ request('cari') }}" class="form-control"
                            placeholder="Cari judul, deskripsi, nama, atau NRP…" maxlength="100">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="tema" class="visually-hidden">Tema agent</label>
                    <select id="tema" name="tema" class="form-select">
                        <option value="">Semua tema agent</option>
                        @foreach ($temaAgent as $tema)
                            <option value="{{ $tema }}" @selected(request('tema') === $tema)>{{ $tema }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-fill">Cari</button>
                    @if (request()->hasAny(['cari', 'tema']))
                        <a href="{{ route('admin.proposal.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 4rem;">#</th>
                            <th>Judul</th>
                            <th>Mahasiswa</th>
                            <th>Tema</th>
                            <th class="text-nowrap">API Key</th>
                            <th class="text-nowrap">Diajukan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($proposals as $proposal)
                            <tr>
                                <td class="text-center text-body-secondary">{{ $proposals->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $proposal->judul }}</div>
                                    @if ($proposal->deskripsi)
                                        <div class="small text-body-secondary">{{ \Illuminate\Support\Str::limit($proposal->deskripsi, 110) }}</div>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    {{ $proposal->user->name }}
                                    @if ($proposal->user->nrp)
                                        <div class="small text-body-secondary">{{ $proposal->user->nrp }}</div>
                                    @endif
                                </td>
                                <td><span class="badge text-bg-secondary">{{ $proposal->tema_agent }}</span></td>
                                <td class="text-nowrap"><code>{{ $proposal->maskedApiKey() }}</code></td>
                                <td class="text-nowrap small">{{ $proposal->created_at->translatedFormat('d M Y') }}</td>
                                <td class="text-end text-nowrap">
                                    @can('proposal.ubah')
                                        <a href="{{ route('admin.proposal.edit', $proposal) }}" class="btn btn-sm btn-outline-primary" title="Ubah">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                            <span class="visually-hidden">Ubah</span>
                                        </a>
                                    @endcan
                                    <x-admin.tombol-hapus :action="route('admin.proposal.destroy', $proposal)" :nama="'proposal “'.$proposal->judul.'”'" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-body-secondary py-5">
                                    <i class="bi bi-inbox fs-3 d-block mb-2" aria-hidden="true"></i>
                                    Tidak ada proposal yang cocok dengan pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="small text-body-secondary">
                Menampilkan {{ $proposals->firstItem() ?? 0 }}–{{ $proposals->lastItem() ?? 0 }} dari {{ $proposals->total() }} proposal
            </span>
            {{-- withQueryString() di controller membuat link ini tetap membawa ?cari=&tema= --}}
            {{ $proposals->links('pagination::bootstrap-5') }}
        </div>
    </div>
@stop
