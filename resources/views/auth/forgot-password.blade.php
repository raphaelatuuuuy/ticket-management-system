<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Forgot Password') }}</h2>
        <p class="mt-2 text-gray-600">{{ __('Enter your email and we\'ll send you a reset link.') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-gray-900">
                {{ __('Back to Login') }}
            </a>
            <x-primary-button id="sendResetBtn">
                <span id="btnText">{{ __('Send Reset Link') }}</span>
                <span id="btnCountdown" class="hidden"></span>
            </x-primary-button>
        </div>
    </form>

    <script>
        (function() {
            const btn = document.getElementById('sendResetBtn');
            const btnText = document.getElementById('btnText');
            const btnCountdown = document.getElementById('btnCountdown');
            const storageKey = 'forgot_password_cooldown';
            
            function startCountdown(seconds) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btnText.classList.add('hidden');
                btnCountdown.classList.remove('hidden');
                
                const endTime = Date.now() + seconds * 1000;
                localStorage.setItem(storageKey, endTime);
                
                function updateTimer() {
                    const remaining = Math.ceil((endTime - Date.now()) / 1000);
                    if (remaining <= 0) {
                        localStorage.removeItem(storageKey);
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                        btnText.classList.remove('hidden');
                        btnCountdown.classList.add('hidden');
                        return;
                    }
                    btnCountdown.textContent = remaining + 's';
                    requestAnimationFrame(updateTimer);
                }
                updateTimer();
            }
            
            const savedEnd = localStorage.getItem(storageKey);
            if (savedEnd && Date.now() < parseInt(savedEnd)) {
                startCountdown(Math.ceil((parseInt(savedEnd) - Date.now()) / 1000));
            }
            
            document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
                if (btn.disabled) {
                    e.preventDefault();
                    return;
                }
                startCountdown(30);
            });
        })();
    </script>
</x-guest-layout>
