<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Dashboard Portofolio
        </h2>
    </x-slot>

    <div class="py-12">
        @if (session('status'))
            <div class="mx-auto mb-6 max-w-7xl sm:px-6 lg:px-8">
                <x-status-banner type="success" title="Berhasil">{{ session('status') }}</x-status-banner>
            </div>
        @endif

        <div class="mx-auto grid max-w-7xl gap-6 sm:px-6 lg:grid-cols-3 lg:px-8">
            {{-- Profil diri --}}
            <section class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="h-20 bg-linear-to-r from-teal-600 to-sky-600"></div>
                <div class="px-6 pb-6">
                    <div class="-mt-10 mb-4 flex size-20 items-center justify-center rounded-full border-4 border-white bg-slate-800 text-2xl font-bold text-white">
                        {{ $user->inisial() }}
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-teal-700">{{ $peran }}</p>

                    <dl class="mt-4 space-y-3 text-sm text-gray-700">
                        <div>
                            <dt class="font-semibold text-gray-500">Email</dt>
                            <dd class="break-all">{{ $user->email }}</dd>
                        </div>
                        @if ($user->nrp)
                            <div>
                                <dt class="font-semibold text-gray-500">NRP</dt>
                                <dd>{{ $user->nrp }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="font-semibold text-gray-500">Kampus</dt>
                            <dd>Institut Teknologi Sepuluh Nopember (ITS)</dd>
                        </div>
                        @if ($user->program_studi)
                            <div>
                                <dt class="font-semibold text-gray-500">Program Studi</dt>
                                <dd>{{ $user->program_studi }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="font-semibold text-gray-500">Bergabung</dt>
                            <dd>{{ $user->created_at->translatedFormat('d F Y') }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Jumlah proposal</dt>
                            <dd>{{ $projects->count() }}</dd>
                        </div>
                    </dl>

                    @if ($user->bio)
                        <p class="mt-4 text-sm leading-relaxed text-gray-600">{{ $user->bio }}</p>
                    @endif

                    @foreach (['Minat' => $user->minat, 'Keahlian' => $user->keahlian] as $label => $daftar)
                        @if (! empty($daftar))
                            <p class="mt-4 mb-1 text-sm font-semibold text-gray-500">{{ $label }}</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($daftar as $item)
                                    <span class="rounded-full bg-teal-50 px-3 py-1 text-xs text-teal-800">{{ $item }}</span>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>

            {{-- Proposal project milik user yang login, terbaru di atas --}}
            <section class="bg-white p-6 shadow-sm sm:rounded-lg lg:col-span-2">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Proposal Ide Proyek Agentic AI</h3>

                @forelse ($projects as $project)
                    <article class="border-b border-gray-100 py-4 first:pt-0 last:border-0 last:pb-0">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <h4 class="font-semibold text-gray-900">{{ $project->judul }}</h4>
                            <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-800">
                                {{ $project->tema_agent }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ $project->deskripsi ?? 'Belum ada deskripsi.' }}</p>
                        <div class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-xs text-gray-500">
                            <span>Dibuat {{ $project->created_at->translatedFormat('d M Y') }}</span>
                            {{-- API key tidak pernah ditampilkan utuh --}}
                            <span>API key: <code class="font-mono">{{ $project->maskedApiKey() }}</code></span>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-gray-500">Belum ada proposal project.</p>
                @endforelse
            </section>

            {{-- Riwayat tugas kuliah (khusus mahasiswa), pengumpulan terbaru di atas --}}
            @if ($user->isMahasiswa())
            <section class="bg-white p-6 shadow-sm sm:rounded-lg lg:col-span-3">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Riwayat Tugas Kuliah</h3>

                @if ($assignments->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada riwayat tugas.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="border-b border-gray-200 text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="py-2 pe-4">Tanggal</th>
                                    <th class="py-2 pe-4">Mata Kuliah</th>
                                    <th class="py-2 pe-4">Tugas</th>
                                    <th class="py-2 pe-4">Nilai</th>
                                    <th class="py-2">Tautan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @foreach ($assignments as $assignment)
                                    <tr class="align-top">
                                        <td class="whitespace-nowrap py-3 pe-4">{{ $assignment->dikumpulkan_pada->translatedFormat('d M Y') }}</td>
                                        <td class="py-3 pe-4">{{ $assignment->mata_kuliah }}</td>
                                        <td class="py-3 pe-4">
                                            <p class="font-medium text-gray-900">{{ $assignment->judul }}</p>
                                            @if ($assignment->deskripsi)
                                                <p class="mt-1 text-xs text-gray-500">{{ $assignment->deskripsi }}</p>
                                            @endif
                                        </td>
                                        <td class="py-3 pe-4">{{ $assignment->nilai ?? 'Belum dinilai' }}</td>
                                        <td class="py-3">
                                            {{-- Hanya https:// agar URL javascript: tidak bisa disisipkan ke href --}}
                                            @if (str_starts_with((string) $assignment->tautan, 'https://'))
                                                <a href="{{ $assignment->tautan }}" target="_blank" rel="noopener noreferrer" class="text-teal-700 underline hover:text-teal-900">Repository</a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
            @endif
        </div>
    </div>
</x-app-layout>
