<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $session->judul }}</h2>
            <a href="{{ route('chat.index') }}" class="text-sm text-teal-700 underline hover:text-teal-900">
                Kembali ke riwayat
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-x-6 gap-y-1 bg-white px-6 py-4 text-sm text-gray-600 shadow-sm sm:rounded-lg">
                <span>Model: <code class="font-mono">{{ $session->model_ai }}</code></span>
                <span>{{ $session->messages->count() }} pesan</span>
                <span>{{ $totalTool }} pemanggilan tool</span>
                <span>{{ number_format($totalToken) }} token</span>
            </div>

            @foreach ($session->messages as $message)
                <article @class([
                    'rounded-lg p-4 shadow-sm',
                    'ms-12 bg-teal-600 text-white' => $message->role === 'user',
                    'me-12 bg-white text-gray-800' => $message->role !== 'user',
                ])>
                    <div @class([
                        'mb-1 flex justify-between text-xs',
                        'text-teal-100' => $message->role === 'user',
                        'text-gray-500' => $message->role !== 'user',
                    ])>
                        <span class="font-semibold">{{ $message->role === 'user' ? 'Anda' : 'Agent AI' }}</span>
                        <span>{{ $message->created_at->format('H:i:s') }}</span>
                    </div>
                    <p class="whitespace-pre-line text-sm leading-relaxed">{{ $message->content }}</p>

                    {{-- Relasi sudah di-eager load, jadi loop ini tidak menambah query --}}
                    @if ($message->toolExecutionLogs->isNotEmpty())
                        <div class="mt-3 space-y-2 border-t border-gray-100 pt-3">
                            @foreach ($message->toolExecutionLogs as $log)
                                <details class="rounded-md bg-slate-50 px-3 py-2 text-xs text-slate-700">
                                    <summary class="flex cursor-pointer flex-wrap items-center gap-2">
                                        <code class="font-mono font-semibold text-slate-900">{{ $log->tool_name }}()</code>
                                        <span class="text-slate-500">
                                            oleh {{ $log->loggable->nama }} ({{ $log->loggable->jenisAgent() }})
                                        </span>
                                        <span @class([
                                            'ms-auto rounded-full px-2 py-0.5 font-medium',
                                            'bg-green-100 text-green-800' => $log->status === 'berhasil',
                                            'bg-red-100 text-red-800' => $log->status === 'gagal',
                                            'bg-amber-100 text-amber-800' => $log->status === 'timeout',
                                        ])>{{ $log->status }}</span>
                                        <span class="text-slate-500">{{ $log->durasi_ms }} ms</span>
                                    </summary>
                                    <dl class="mt-2 grid gap-2 sm:grid-cols-2">
                                        <div>
                                            <dt class="font-semibold">Input</dt>
                                            <dd><pre class="overflow-x-auto whitespace-pre-wrap font-mono">{{ json_encode($log->input, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold">Output</dt>
                                            <dd><pre class="overflow-x-auto whitespace-pre-wrap font-mono">{{ json_encode($log->output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></dd>
                                        </div>
                                    </dl>
                                </details>
                            @endforeach
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</x-app-layout>
