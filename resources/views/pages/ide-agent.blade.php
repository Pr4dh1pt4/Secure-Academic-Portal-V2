@extends('layouts.portfolio')

@section('title', 'Ide-Riset Agentic AI')

@php
    // Tantangan 1: class Tailwind dipilih dari variabel Blade $isDark (?mode=dark)
    $card = $isDark ? 'border-slate-700 bg-slate-900 text-slate-100' : 'border-slate-200 bg-white text-slate-800';
    $muted = $isDark ? 'text-slate-400' : 'text-slate-600';
    $heading = $isDark ? 'text-white' : 'text-slate-900';
    $input = $isDark
        ? 'border-slate-700 bg-slate-800 text-slate-100 placeholder-slate-500'
        : 'border-slate-300 bg-white text-slate-900 placeholder-slate-400';
@endphp

@section('content')
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-teal-500">Proposal Unggulan Agentic AI</p>
            <h1 class="text-3xl font-bold sm:text-4xl {{ $heading }}">{{ $unggulan?->judul ?? 'Belum ada proposal' }}</h1>
            @if ($unggulan)
                <p class="mt-2 text-sm {{ $muted }}">Tema agent: <span class="font-semibold text-teal-500">{{ $unggulan->tema_agent }}</span></p>
            @endif
        </div>
        <a href="{{ route('ide-agent', $isDark ? [] : ['mode' => 'dark']) }}"
           class="rounded-lg border px-4 py-2 text-sm font-medium transition {{ $isDark ? 'border-slate-600 hover:bg-slate-800' : 'border-slate-300 hover:bg-slate-100' }}">
            {{ $isDark ? '☀ Mode Terang' : '☾ Mode Gelap' }}
        </a>
    </div>

    @if ($unggulan?->deskripsi)
        <p class="mb-10 max-w-4xl leading-relaxed {{ $muted }}">{{ $unggulan->deskripsi }}</p>
    @endif

    {{-- Diagram pipeline dari kolom projects.tahapan: vertikal di layar kecil, horizontal di layar besar --}}
    @if (! empty($unggulan?->tahapan))
        <section aria-label="Alur kerja {{ $unggulan->judul }}" class="mb-12">
            <h2 class="mb-4 text-xl font-semibold {{ $heading }}">Alur Kerja Agent</h2>
            <ol class="flex flex-col items-stretch lg:flex-row">
                @foreach ($unggulan->tahapan as $tahap)
                    <li class="flex flex-col items-center lg:flex-1 lg:flex-row">
                        <div class="w-full rounded-xl border p-4 shadow-sm lg:h-full {{ $card }}">
                            <span class="mb-3 flex size-9 items-center justify-center rounded-full bg-teal-600 text-sm font-bold text-white">
                                {{ $loop->iteration }}
                            </span>
                            <h3 class="mb-1 text-sm font-semibold leading-snug">{{ $tahap['judul'] }}</h3>
                            <p class="text-xs leading-relaxed {{ $muted }}">{{ $tahap['keterangan'] }}</p>
                        </div>
                        @unless ($loop->last)
                            <span aria-hidden="true" class="py-1 text-2xl text-teal-500 lg:px-1 lg:py-0">
                                <span class="lg:hidden">↓</span><span class="hidden lg:inline">→</span>
                            </span>
                        @endunless
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    @if ($lainnya->isNotEmpty())
        <section class="mb-12">
            <h2 class="mb-4 text-xl font-semibold {{ $heading }}">Proposal Lainnya</h2>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($lainnya as $project)
                    <article class="rounded-xl border p-5 shadow-sm {{ $card }}">
                        <div class="mb-2 flex flex-wrap items-start justify-between gap-2">
                            <h3 class="font-semibold">{{ $project->judul }}</h3>
                            <span class="rounded-full bg-teal-600/10 px-3 py-1 text-xs font-medium text-teal-500">{{ $project->tema_agent }}</span>
                        </div>
                        <p class="text-sm leading-relaxed {{ $muted }}">{{ $project->deskripsi ?? 'Belum ada deskripsi.' }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Formulir pengajuan ide: disimpan ke tabel projects milik user yang login --}}
    <section class="max-w-2xl rounded-2xl border p-6 shadow-sm {{ $card }}">
        <h2 class="mb-1 text-xl font-semibold {{ $heading }}">Formulir Pengajuan Ide</h2>
        <p class="mb-5 text-sm {{ $muted }}">Punya ide proyek Agentic AI? Ajukan di sini dan ide akan tersimpan di portofolio Anda.</p>

        @guest
            <x-status-banner type="info" title="Masuk terlebih dahulu" :dark="$isDark">
                Ide disimpan sebagai milik akun Anda, jadi silakan
                <a href="{{ route('login') }}" class="font-semibold underline">masuk</a> atau
                <a href="{{ route('register') }}" class="font-semibold underline">daftar</a> dulu.
            </x-status-banner>
        @else
            @if ($errors->any())
                <x-status-banner type="error" title="Periksa kembali isian formulir" :dark="$isDark" class="mb-5">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-status-banner>
            @endif

            <form method="POST" action="{{ route('ide-agent.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="judul" class="mb-1 block text-sm font-medium">Judul Ide</label>
                    <input id="judul" name="judul" type="text" value="{{ old('judul') }}" required maxlength="255"
                           placeholder="Contoh: Deteksi regresi visual otomatis"
                           class="w-full rounded-lg border px-3 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/30 focus:outline-none {{ $input }}">
                </div>

                <div>
                    <label for="deskripsi" class="mb-1 block text-sm font-medium">Deskripsi <span class="{{ $muted }}">(opsional)</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" maxlength="2000"
                              placeholder="Jelaskan ide secara singkat..."
                              class="w-full rounded-lg border px-3 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/30 focus:outline-none {{ $input }}">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="tema_agent" class="mb-1 block text-sm font-medium">Tema Agent</label>
                        <select id="tema_agent" name="tema_agent"
                                class="w-full rounded-lg border px-3 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/30 focus:outline-none {{ $input }}">
                            @foreach ($temaAgent as $tema)
                                <option value="{{ $tema }}" @selected(old('tema_agent', 'Ollama') === $tema)>{{ $tema }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="api_key_secure" class="mb-1 block text-sm font-medium">API Key</label>
                        <input id="api_key_secure" name="api_key_secure" type="password" required minlength="8" maxlength="255"
                               autocomplete="off" placeholder="Disimpan terenkripsi"
                               class="w-full rounded-lg border px-3 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/30 focus:outline-none {{ $input }}">
                    </div>
                </div>

                <button type="submit" class="rounded-lg bg-teal-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-teal-700">
                    Simpan Ide
                </button>
            </form>
        @endguest
    </section>
@endsection
