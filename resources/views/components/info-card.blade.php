@props([
    'name',
    'campus',
    'major',
    'interests' => [],
    'photo' => null,
])

@php
    $initials = collect(explode(' ', $name))->take(2)->map(fn ($word) => mb_substr($word, 0, 1))->implode('');
@endphp

<div {{ $attributes->class('overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm') }}>
    <div class="h-24 bg-linear-to-r from-teal-600 to-sky-600"></div>
    <div class="px-6 pb-6">
        <div class="-mt-12 mb-4">
            @if ($photo)
                <img src="{{ $photo }}" alt="Foto {{ $name }}" class="size-24 rounded-full border-4 border-white object-cover">
            @else
                <div class="flex size-24 items-center justify-center rounded-full border-4 border-white bg-slate-800 text-3xl font-bold text-white">
                    {{ $initials }}
                </div>
            @endif
        </div>

        <h2 class="text-2xl font-bold text-slate-900">{{ $name }}</h2>
        <p class="text-teal-700">{{ $major }}</p>

        <dl class="mt-4 space-y-3 text-sm text-slate-700">
            <div>
                <dt class="font-semibold text-slate-500">Kampus</dt>
                <dd>{{ $campus }}</dd>
            </div>
            @if (count($interests))
                <div>
                    <dt class="mb-1 font-semibold text-slate-500">Minat</dt>
                    <dd class="flex flex-wrap gap-2">
                        @foreach ($interests as $interest)
                            <span class="rounded-full bg-teal-50 px-3 py-1 text-teal-800">{{ $interest }}</span>
                        @endforeach
                    </dd>
                </div>
            @endif
        </dl>

        {{ $slot }}
    </div>
</div>
