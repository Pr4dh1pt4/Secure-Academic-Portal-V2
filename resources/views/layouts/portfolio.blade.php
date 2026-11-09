@php
    $isDark = $isDark ?? false;
    $navLinks = [
        ['route' => 'portal', 'label' => 'Portal'],
        ['route' => 'beranda', 'label' => 'Beranda'],
        ['route' => 'profil', 'label' => 'Profil'],
        ['route' => 'ide-agent', 'label' => 'Ide-Riset'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') | Profil Akademik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col font-sans antialiased {{ $isDark ? 'bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-800' }}">
    <header class="border-b {{ $isDark ? 'border-slate-800 bg-slate-900' : 'border-slate-200 bg-white' }}">
        <nav class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-4">
            <a href="{{ route('beranda') }}" class="text-lg font-bold tracking-tight">
                <span class="text-teal-600">{{ $owner->inisial() }}</span> · Profil Akademik
            </a>
            <ul class="flex gap-1 text-sm font-medium">
                @foreach ($navLinks as $link)
                    @php $active = request()->routeIs($link['route']); @endphp
                    <li>
                        <a href="{{ route($link['route']) }}"
                           @class([
                               'rounded-md px-3 py-2 transition',
                               'bg-teal-600 text-white' => $active,
                               'hover:bg-slate-800' => ! $active && $isDark,
                               'hover:bg-slate-100' => ! $active && ! $isDark,
                           ])>
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
                <li>
                    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
                       class="rounded-md border border-teal-600 px-3 py-2 text-teal-600 transition hover:bg-teal-600 hover:text-white">
                        {{ auth()->check() ? 'Dashboard' : 'Masuk' }}
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10">
        @yield('content')
    </main>

    <footer class="border-t py-6 text-center text-sm {{ $isDark ? 'border-slate-800 bg-slate-900 text-slate-400' : 'border-slate-200 bg-white text-slate-500' }}">
        &copy; {{ date('Y') }} {{ $owner->name }} · Institut Teknologi Sepuluh Nopember (ITS)
    </footer>
</body>
</html>
