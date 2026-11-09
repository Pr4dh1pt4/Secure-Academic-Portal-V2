{{--
    Tombol "Hapus Data" yang hanya dirender untuk peran Admin murni:
    user wajib berperan Admin DAN memegang permission data.hapus.
    Aturan yang sama dicek ulang di controller lewat middleware
    ['role:Admin', 'permission:data.hapus'], sehingga menembak URL langsung tetap ditolak (403).
--}}
@props(['action', 'nama' => 'data ini'])

@role('Admin')
    @can('data.hapus')
        <form method="POST" action="{{ $action }}" class="d-inline"
            onsubmit="return confirm(@js('Hapus '.$nama.'? Tindakan ini tidak bisa dibatalkan.'))">
            @csrf
            @method('DELETE')
            <button type="submit" {{ $attributes->merge(['class' => 'btn btn-sm btn-outline-danger']) }}
                title="Hapus" data-testid="tombol-hapus">
                <i class="bi bi-trash" aria-hidden="true"></i>
                <span class="visually-hidden">Hapus</span>
            </button>
        </form>
    @endcan
@endrole
