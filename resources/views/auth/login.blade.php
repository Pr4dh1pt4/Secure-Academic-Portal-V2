<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    {{-- Login instan akun uji, khusus demo (lihat config/demo.php). --}}
    @if (\App\Http\Controllers\Auth\DemoLoginController::aktif())
        <div class="mt-6 border-t border-gray-200 pt-5" data-testid="demo-login">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Login instan akun uji</p>
            <p class="mt-1 text-xs text-gray-500">Khusus demo evaluasi dosen/asdos. Mati otomatis di production.</p>

            <div class="mt-3 grid gap-2">
                @foreach (config('demo.accounts') as $kunci => $akun)
                    <form method="POST" action="{{ route('login.demo', $kunci) }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center justify-between rounded-md border border-gray-300 px-3 py-2 text-left text-sm hover:border-indigo-500 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span>
                                <span class="font-semibold text-gray-800">Masuk sebagai {{ $akun['label'] }}</span>
                                <span class="block text-xs text-gray-500">{{ $akun['keterangan'] }}</span>
                            </span>
                            <span class="text-xs text-gray-400">{{ $akun['email'] }}</span>
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif
</x-guest-layout>
