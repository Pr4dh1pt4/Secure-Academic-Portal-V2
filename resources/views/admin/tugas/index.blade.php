@extends('adminlte::page')

@section('title', 'Riwayat Tugas')

@section('content_header')
    <h3 class="mb-0">Riwayat Tugas</h3>
@stop

@section('content')
    @include('admin.partials.flash')

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.tugas.index') }}" class="row g-2 align-items-center" role="search">
                <div class="col-lg-4">
                    <label for="cari" class="visually-hidden">Kata kunci</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="cari" name="cari" value="{{ request('cari') }}" class="form-control"
                            placeholder="Cari judul, nama, atau NRP…" maxlength="100">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label for="mata_kuliah" class="visually-hidden">Mata kuliah</label>
                    <select id="mata_kuliah" name="mata_kuliah" class="form-select">
                        <option value="">Semua mata kuliah</option>
                        @foreach ($daftarMataKuliah as $mk)
                            <option value="{{ $mk }}" @selected(request('mata_kuliah') === $mk)>{{ $mk }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="status" class="visually-hidden">Status nilai</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="dinilai" @selected(request('status') === 'dinilai')>Sudah dinilai</option>
                        <option value="belum" @selected(request('status') === 'belum')>Belum dinilai</option>
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-fill">Cari</button>
                    @if (request()->hasAny(['cari', 'mata_kuliah', 'status']))
                        <a href="{{ route('admin.tugas.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                            <th>Tugas</th>
                            <th>Mahasiswa</th>
                            <th class="text-nowrap">Dikumpulkan</th>
                            <th class="text-center">Nilai</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tugas as $item)
                            <tr>
                                <td class="text-center text-body-secondary">{{ $tugas->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $item->judul }}</div>
                                    <div class="small text-body-secondary">{{ $item->mata_kuliah }}</div>
                                </td>
                                <td class="text-nowrap">
                                    {{ $item->user->name }}
                                    @if ($item->user->nrp)
                                        <div class="small text-body-secondary">{{ $item->user->nrp }}</div>
                                    @endif
                                </td>
                                <td class="text-nowrap small">{{ $item->dikumpulkan_pada->translatedFormat('d M Y') }}</td>
                                <td class="text-center">
                                    @if ($item->nilai !== null)
                                        <span class="badge {{ $item->nilai >= 85 ? 'text-bg-success' : 'text-bg-primary' }}">{{ $item->nilai }}</span>
                                    @else
                                        <span class="badge text-bg-light border">Belum</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <x-admin.tombol-hapus :action="route('admin.tugas.destroy', $item)" :nama="'tugas “'.$item->judul.'”'" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-body-secondary py-5">
                                    <i class="bi bi-inbox fs-3 d-block mb-2" aria-hidden="true"></i>
                                    Tidak ada tugas yang cocok dengan pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="small text-body-secondary">
                Menampilkan {{ $tugas->firstItem() ?? 0 }}–{{ $tugas->lastItem() ?? 0 }} dari {{ $tugas->total() }} tugas
            </span>
            {{ $tugas->links('pagination::bootstrap-5') }}
        </div>
    </div>
@stop
