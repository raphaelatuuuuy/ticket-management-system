<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        @if (Route::has('password.request'))
            <div class="mt-2 text-right">
                <a class="text-sm text-indigo-600 hover:text-indigo-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            </div>
        @endif

        <div class="flex justify-center mt-6">
            <x-primary-button class="w-full py-3 text-center">
                <span class="block w-full text-center">{{ __('Log in') }}</span>
            </x-primary-button>
        </div>

        <div class="block mt-4 text-center">
            <span class="text-sm text-gray-600">
                {{ __("Don't have an account yet?") }}
                <a href="{{ route('register') }}" class="text-indigo-600 underline hover:underline">
                    {{ __('Register here') }}
                </a>
            </span>
        </div>
    </form>
</x-guest-layout>
