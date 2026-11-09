<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Secure Academic Portal | Informatika ITS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-800 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-4">
            <a href="{{ route('portal') }}" class="text-lg font-bold tracking-tight">
                <span class="text-teal-600">SAP</span> · Secure Academic Portal
            </a>
            <div class="flex items-center gap-2 text-sm font-medium">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-md bg-teal-600 px-4 py-2 text-white transition hover:bg-teal-700">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-md px-4 py-2 transition hover:bg-slate-100">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-teal-600 px-4 py-2 text-white transition hover:bg-teal-700">Daftar</a>
                @endauth
            </div>
        </nav>
    </header>

    <main class="flex-1">
        {{-- Hero --}}
        <section class="bg-linear-to-r from-teal-600 to-sky-600 text-white">
            <div class="mx-auto max-w-6xl px-4 py-16">
                <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-teal-100">Pemrograman Berbasis Kerangka Kerja · Institut Teknologi Sepuluh Nopember</p>
                <h1 class="mb-4 max-w-3xl text-3xl font-bold sm:text-5xl">Portal Portofolio Akademik Mahasiswa</h1>
                <p class="max-w-2xl text-lg leading-relaxed text-teal-50">
                    Tempat mahasiswa menyimpan profil diri, riwayat tugas kuliah, dan proposal ide proyek Agentic AI
                    secara aman. API key proposal disimpan terenkripsi, dan setiap mahasiswa hanya dapat mengelola datanya sendiri.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-white px-5 py-3 font-semibold text-teal-700 shadow-sm transition hover:bg-teal-50">Buka Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg bg-white px-5 py-3 font-semibold text-teal-700 shadow-sm transition hover:bg-teal-50">Masuk ke Portal</a>
                        <a href="{{ route('register') }}" class="rounded-lg border border-white/70 px-5 py-3 font-semibold text-white transition hover:bg-white/10">Daftar Akun Mahasiswa</a>
                    @endauth
                </div>
            </div>
        </section>

        <div class="mx-auto max-w-6xl space-y-12 px-4 py-12">
            {{-- Statistik agregat --}}
            <section aria-label="Statistik portal">
                <dl class="grid gap-4 sm:grid-cols-3">
                    @foreach ($statistik as $label => $jumlah)
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <dt class="text-sm font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                            <dd class="mt-1 text-4xl font-bold text-teal-700">{{ $jumlah }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <div class="grid gap-6 lg:grid-cols-3">
                {{-- Proposal terbaru seluruh mahasiswa --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <h2 class="mb-4 text-xl font-semibold text-slate-900">Proposal Agentic AI Terbaru</h2>

                    @forelse ($proposalTerbaru as $project)
                        <article class="border-b border-slate-100 py-3 first:pt-0 last:border-0 last:pb-0">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <h3 class="font-medium text-slate-900">{{ $project->judul }}</h3>
                                <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-800">{{ $project->tema_agent }}</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $project->user->name }}@if ($project->user->program_studi) · {{ $project->user->program_studi }}@endif
                                · {{ $project->created_at->translatedFormat('d M Y') }}
                            </p>
                        </article>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada proposal.</p>
                    @endforelse
                </section>

                <div class="space-y-6">
                    {{-- Sebaran mahasiswa per program studi --}}
                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-semibold text-slate-900">Mahasiswa per Program Studi</h2>
                        <ul class="space-y-3 text-sm">
                            @forelse ($perProdi as $prodi)
                                <li>
                                    <div class="mb-1 flex justify-between">
                                        <span>{{ $prodi->program_studi }}</span>
                                        <span class="font-semibold text-slate-900">{{ $prodi->jumlah }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-slate-100">
                                        <div class="h-2 rounded-full bg-teal-500" style="width: {{ round($prodi->jumlah / max($statistik['Mahasiswa'], 1) * 100) }}%"></div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-slate-500">Belum ada data.</li>
                            @endforelse
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <footer class="border-t border-slate-200 bg-white py-6 text-center text-sm text-slate-500">
        &copy; {{ date('Y') }} Secure Academic Portal · Departemen Teknik Informatika · Institut Teknologi Sepuluh Nopember (ITS)
    </footer>
</body>
</html>
