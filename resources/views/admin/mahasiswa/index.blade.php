@extends('adminlte::page')

@section('title', 'Mahasiswa')

@section('content_header')
    <h3 class="mb-0">Data Mahasiswa</h3>
@stop

@section('content')
    @include('admin.partials.flash')

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.mahasiswa.index') }}" class="row g-2 align-items-center" role="search">
                <div class="col-md-6">
                    <label for="cari" class="visually-hidden">Kata kunci</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="cari" name="cari" value="{{ request('cari') }}" class="form-control"
                            placeholder="Cari nama, NRP, atau email…" maxlength="100">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="prodi" class="visually-hidden">Program studi</label>
                    <select id="prodi" name="prodi" class="form-select">
                        <option value="">Semua program studi</option>
                        @foreach ($daftarProdi as $prodi)
                            <option value="{{ $prodi }}" @selected(request('prodi') === $prodi)>{{ $prodi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-fill">Cari</button>
                    @if (request()->hasAny(['cari', 'prodi']))
                        <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                            <th>Nama</th>
                            <th>NRP</th>
                            <th>Program Studi</th>
                            <th class="text-center">Proposal</th>
                            <th class="text-center">Tugas</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswa as $mhs)
                            <tr>
                                <td class="text-center text-body-secondary">{{ $mahasiswa->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $mhs->name }}</div>
                                    <div class="small text-body-secondary">{{ $mhs->email }}</div>
                                </td>
                                <td><code>{{ $mhs->nrp ?? '-' }}</code></td>
                                <td>{{ $mhs->program_studi ?? '-' }}</td>
                                <td class="text-center">{{ $mhs->projects_count }}</td>
                                <td class="text-center">{{ $mhs->assignments_count }}</td>
                                <td class="text-end">
                                    <x-admin.tombol-hapus :action="route('admin.mahasiswa.destroy', $mhs)"
                                        :nama="'akun '.$mhs->name.' beserta seluruh proposal, tugas, dan obrolannya'" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-body-secondary py-5">
                                    <i class="bi bi-inbox fs-3 d-block mb-2" aria-hidden="true"></i>
                                    Tidak ada mahasiswa yang cocok dengan pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="small text-body-secondary">
                Menampilkan {{ $mahasiswa->firstItem() ?? 0 }}–{{ $mahasiswa->lastItem() ?? 0 }} dari {{ $mahasiswa->total() }} mahasiswa
            </span>
            {{ $mahasiswa->links('pagination::bootstrap-5') }}
        </div>
    </div>
@stop
