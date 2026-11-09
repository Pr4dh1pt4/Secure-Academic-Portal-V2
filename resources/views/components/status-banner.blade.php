@props([
    'type' => 'info',
    'title' => null,
    'dark' => false,
])

@php
    $styles = [
        'success' => ['light' => 'border-emerald-300 bg-emerald-50 text-emerald-800', 'dark' => 'border-emerald-700 bg-emerald-950 text-emerald-200', 'icon' => '✓'],
        'info' => ['light' => 'border-sky-300 bg-sky-50 text-sky-800', 'dark' => 'border-sky-700 bg-sky-950 text-sky-200', 'icon' => 'i'],
        'warning' => ['light' => 'border-amber-300 bg-amber-50 text-amber-800', 'dark' => 'border-amber-700 bg-amber-950 text-amber-200', 'icon' => '!'],
        'error' => ['light' => 'border-rose-300 bg-rose-50 text-rose-800', 'dark' => 'border-rose-700 bg-rose-950 text-rose-200', 'icon' => '✕'],
    ];
    $style = $styles[$type] ?? $styles['info'];
@endphp

<div role="{{ $type === 'error' ? 'alert' : 'status' }}"
     {{ $attributes->class(['flex items-start gap-3 rounded-lg border px-4 py-3', $dark ? $style['dark'] : $style['light']]) }}>
    <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full border border-current text-xs font-bold">
        {{ $style['icon'] }}
    </span>
    <div class="text-sm">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div>{{ $slot }}</div>
    </div>
</div>
