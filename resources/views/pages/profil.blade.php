@extends('layouts.portfolio')

@section('title', 'Profil Mahasiswa')

@section('content')
    <h1 class="mb-6 text-3xl font-bold text-slate-900">Profil Mahasiswa</h1>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-info-card
            class="lg:col-span-2"
            :name="$owner->name"
            campus="Institut Teknologi Sepuluh Nopember (ITS), Surabaya"
            :major="$owner->program_studi"
            :interests="$owner->minat ?? []"
        >
            @if ($owner->bio)
                <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $owner->bio }}</p>
            @endif
        </x-info-card>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Skill</h2>
            <ul class="space-y-2 text-sm">
                @forelse ($owner->keahlian ?? [] as $skill)
                    <li class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-teal-500"></span>
                        {{ $skill }}
                    </li>
                @empty
                    <li class="text-slate-500">Belum ada data keahlian.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-900">Riwayat Tugas Kuliah</h2>

        @forelse ($assignments as $assignment)
            <article class="border-b border-slate-100 py-4 first:pt-0 last:border-0 last:pb-0">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h3 class="font-semibold text-slate-900">{{ $assignment->judul }}</h3>
                    <time class="text-xs text-slate-500" datetime="{{ $assignment->dikumpulkan_pada->toDateString() }}">
                        {{ $assignment->dikumpulkan_pada->translatedFormat('d F Y') }}
                    </time>
                </div>
                <p class="text-sm text-teal-700">{{ $assignment->mata_kuliah }}</p>
                @if ($assignment->deskripsi)
                    <p class="mt-1 text-sm text-slate-600">{{ $assignment->deskripsi }}</p>
                @endif
                {{-- Hanya https:// agar URL javascript: tidak bisa disisipkan ke href --}}
                @if (str_starts_with((string) $assignment->tautan, 'https://'))
                    <a href="{{ $assignment->tautan }}" target="_blank" rel="noopener noreferrer" class="mt-1 inline-block text-sm text-teal-700 underline hover:text-teal-900">Lihat repository</a>
                @endif
            </article>
        @empty
            <p class="text-sm text-slate-500">Belum ada riwayat tugas.</p>
        @endforelse
    </section>
@endsection
