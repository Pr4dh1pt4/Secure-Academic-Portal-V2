@extends('layouts.portfolio')

@section('title', 'Beranda')

@section('content')
    <x-status-banner type="{{ $user ? 'success' : 'info' }}" title="{{ $user ? 'Selamat datang, '.$user.'!' : 'Selamat datang!' }}" class="mb-8">
        @if ($user)
            Senang melihatmu di portofolio akademik saya.
        @else
            Coba tambahkan <code class="font-mono">?user=NamaKamu</code> di URL untuk sambutan personal.
        @endif
    </x-status-banner>

    <section class="mb-10">
        <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-teal-600">Secure Academic Portal · PBKK</p>
        <h1 class="mb-4 text-3xl font-bold text-slate-900 sm:text-4xl">Portofolio Akademik {{ $owner->name }}</h1>
        <p class="max-w-3xl text-lg leading-relaxed text-slate-600">{{ $owner->bio }}</p>

        <dl class="mt-6 flex flex-wrap gap-4">
            <div class="rounded-xl border border-slate-200 bg-white px-5 py-3 shadow-sm">
                <dt class="text-xs font-semibold uppercase text-slate-500">Proposal Agentic AI</dt>
                <dd class="text-2xl font-bold text-teal-700">{{ $owner->projects_count }}</dd>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white px-5 py-3 shadow-sm">
                <dt class="text-xs font-semibold uppercase text-slate-500">Riwayat Tugas</dt>
                <dd class="text-2xl font-bold text-teal-700">{{ $owner->assignments_count }}</dd>
            </div>
        </dl>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
        @foreach ([
            ['route' => 'profil', 'title' => 'Profil Mahasiswa', 'desc' => 'Data diri, minat, keahlian, dan riwayat tugas.'],
            ['route' => 'ide-agent', 'title' => 'Ide-Riset Agentic AI', 'desc' => 'Proposal unggulan beserta alur kerja agennya.'],
            ['route' => 'ide-agent', 'title' => 'Formulir Ide', 'desc' => 'Ajukan proposal ide baru ke portofolio Anda.'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-teal-400 hover:shadow-md">
                <h2 class="mb-1 font-semibold text-slate-900 group-hover:text-teal-700">{{ $item['title'] }} →</h2>
                <p class="text-sm text-slate-600">{{ $item['desc'] }}</p>
            </a>
        @endforeach
    </section>
@endsection
