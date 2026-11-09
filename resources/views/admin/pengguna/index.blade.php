@extends('adminlte::page')

@section('title', 'Peran Pengguna')

@section('content_header')
    <h3 class="mb-0">Peran Pengguna</h3>
@stop

@section('content')
    @include('admin.partials.flash')

    <div class="callout callout-info mb-3">
        <p class="mb-0 small">
            <strong>Admin</strong>: akses penuh, termasuk hapus data dan pengaturan peran.
            <strong>Staf</strong>: melihat data serta menambah dan mengubah proposal.
            <strong>Tanpa peran</strong>: tidak bisa membuka panel backoffice.
            Perubahan disimpan ke tabel <code>model_has_roles</code>.
        </p>
    </div>

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.pengguna.index') }}" class="row g-2 align-items-center" role="search">
                <div class="col-md-6">
                    <label for="cari" class="visually-hidden">Kata kunci</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="cari" name="cari" value="{{ request('cari') }}" class="form-control"
                            placeholder="Cari nama atau email…" maxlength="100">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="filter-peran" class="visually-hidden">Peran</label>
                    <select id="filter-peran" name="peran" class="form-select">
                        <option value="">Semua peran</option>
                        @foreach ($daftarPeran as $peran)
                            <option value="{{ $peran }}" @selected(request('peran') === $peran)>{{ $peran }}</option>
                        @endforeach
                        <option value="tanpa" @selected(request('peran') === 'tanpa')>Tanpa peran</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-fill">Cari</button>
                    @if (request()->hasAny(['cari', 'peran']))
                        <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Peran saat ini</th>
                            <th style="min-width: 16rem;">Ubah peran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengguna as $user)
                            @php $peranUser = $user->roles->pluck('name')->first(); @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="small text-body-secondary">{{ $user->email }}</div>
                                </td>
                                <td>
                                    @forelse ($user->roles as $role)
                                        <span class="badge {{ $role->name === 'Admin' ? 'text-bg-danger' : 'text-bg-info' }}">{{ $role->name }}</span>
                                    @empty
                                        <span class="badge text-bg-light border">Tanpa peran</span>
                                    @endforelse
                                </td>
                                <td>
                                    @if ($user->is(auth()->user()))
                                        <span class="small text-body-secondary">Akun Anda sendiri</span>
                                    @else
                                        <form method="POST" action="{{ route('admin.pengguna.update', $user) }}" class="d-flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <label for="peran-{{ $user->id }}" class="visually-hidden">Peran {{ $user->name }}</label>
                                            <select id="peran-{{ $user->id }}" name="peran" class="form-select form-select-sm">
                                                <option value="">Tanpa peran</option>
                                                @foreach ($daftarPeran as $peran)
                                                    <option value="{{ $peran }}" @selected($peranUser === $peran)>{{ $peran }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Simpan</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="small text-body-secondary">
                Menampilkan {{ $pengguna->firstItem() ?? 0 }}–{{ $pengguna->lastItem() ?? 0 }} dari {{ $pengguna->total() }} pengguna
            </span>
            {{ $pengguna->links('pagination::bootstrap-5') }}
        </div>
    </div>
@stop
