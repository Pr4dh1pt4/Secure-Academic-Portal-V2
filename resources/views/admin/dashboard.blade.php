@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h3 class="mb-0">Dashboard</h3>
        </div>
        <div class="col-sm-6 text-sm-end text-body-secondary small pt-2">
            Selamat datang, <strong>{{ auth()->user()->name }}</strong>
            @foreach ($peran as $namaPeran)
                <span class="badge {{ $namaPeran === 'Admin' ? 'text-bg-danger' : 'text-bg-info' }} ms-1">{{ $namaPeran }}</span>
            @endforeach
        </div>
    </div>
@stop

@section('content')
    @include('admin.partials.flash')

    {{-- Widget statistik agregat: total dihitung dengan count() di controller. --}}
    <div class="row">
        @foreach ($widgets as $widget)
            @php
                $bisaBuka = $widget['route'] && auth()->user()->can($widget['permission']);
            @endphp
            <div class="col-lg-3 col-6">
                <x-adminlte-small-box
                    :title="number_format($widget['total'], 0, ',', '.')"
                    :text="$widget['judul']"
                    :icon="$widget['ikon']"
                    :theme="$widget['warna']"
                    :url="$bisaBuka ? route($widget['route']) : null"
                    :url-text="$bisaBuka ? 'Lihat data' : null"
                    data-testid="widget-{{ \Illuminate\Support\Str::slug($widget['judul']) }}">
                    @unless ($bisaBuka)
                        <x-slot name="footerSlot">{{ $widget['catatan'] ?? 'Akses terbatas' }}</x-slot>
                    @endunless
                </x-adminlte-small-box>
            </div>
        @endforeach
    </div>

    <div class="row">
        {{-- Proposal terbaru --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-clock-history me-1" aria-hidden="true"></i> Proposal Terbaru</h3>
                    @can('proposal.lihat')
                        <div class="card-tools">
                            <a href="{{ route('admin.proposal.index') }}" class="btn btn-sm btn-outline-primary">Semua proposal</a>
                        </div>
                    @endcan
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Mahasiswa</th>
                                    <th>Tema</th>
                                    <th class="text-nowrap">Diajukan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($proposalTerbaru as $proposal)
                                    <tr>
                                        <td>{{ \Illuminate\Support\Str::limit($proposal->judul, 60) }}</td>
                                        <td class="text-nowrap">{{ $proposal->user->name }}</td>
                                        <td><span class="badge text-bg-secondary">{{ $proposal->tema_agent }}</span></td>
                                        <td class="text-nowrap small text-body-secondary">{{ $proposal->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-body-secondary py-4">Belum ada proposal.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Sebaran tema agent (GROUP BY + COUNT) --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-bar-chart me-1" aria-hidden="true"></i> Proposal per Tema Agent</h3>
                </div>
                <div class="card-body">
                    @php $maks = max(1, (int) $perTema->max('jumlah')); @endphp
                    @forelse ($perTema as $baris)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small">
                                <span>{{ $baris->tema_agent }}</span>
                                <span class="fw-semibold">{{ $baris->jumlah }}</span>
                            </div>
                            <div class="progress" style="height: 6px;" role="progressbar"
                                aria-label="{{ $baris->tema_agent }}" aria-valuenow="{{ $baris->jumlah }}" aria-valuemin="0" aria-valuemax="{{ $maks }}">
                                <div class="progress-bar" style="width: {{ round($baris->jumlah / $maks * 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-body-secondary mb-0">Belum ada data.</p>
                    @endforelse
                </div>
            </div>

            {{-- Hak akses user yang login (dibaca dari tabel Spatie) --}}
            <div class="card mb-4" data-testid="kartu-hak-akses">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-key me-1" aria-hidden="true"></i> Hak Akses Anda</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-1">
                        @foreach ($hakAkses as $permission)
                            <span class="badge {{ in_array($permission, ['data.hapus', 'pengguna.kelola']) ? 'text-bg-danger' : 'text-bg-light border' }}">{{ $permission }}</span>
                        @endforeach
                    </div>
                    @unlessrole('Admin')
                        <p class="small text-body-secondary mt-3 mb-0">
                            <i class="bi bi-info-circle" aria-hidden="true"></i>
                            Peran Staf tidak bisa menghapus data maupun mengatur peran pengguna.
                        </p>
                    @endunlessrole
                </div>
            </div>
        </div>
    </div>
@stop
