<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Riwayat Obrolan Agent AI
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <section class="bg-white p-6 shadow-sm sm:rounded-lg">
                @forelse ($sessions as $session)
                    <a href="{{ route('chat.show', $session) }}"
                       class="block border-b border-gray-100 py-4 first:pt-0 last:border-0 last:pb-0 hover:bg-gray-50">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <h3 class="font-semibold text-gray-900">{{ $session->judul }}</h3>
                            <span @class([
                                'rounded-full px-3 py-1 text-xs font-medium',
                                'bg-teal-50 text-teal-800' => $session->status === 'aktif',
                                'bg-gray-100 text-gray-600' => $session->status !== 'aktif',
                            ])>
                                {{ ucfirst($session->status) }}
                            </span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-x-6 gap-y-1 text-xs text-gray-500">
                            <span>Model: <code class="font-mono">{{ $session->model_ai }}</code></span>
                            <span>{{ $session->messages_count }} pesan</span>
                            <span>{{ $session->tool_execution_logs_count }} pemanggilan tool</span>
                            <span>Terakhir aktif {{ $session->updated_at->translatedFormat('d M Y H:i') }}</span>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-gray-500">Belum ada sesi obrolan.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
