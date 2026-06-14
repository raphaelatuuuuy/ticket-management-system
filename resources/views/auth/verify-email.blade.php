<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Verify Email') }}</h2>
        <p class="mt-2 text-gray-600">
            {{ __('Click the link in your email to verify your account.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to your email.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
            @csrf

            <div>
                <x-primary-button id="resendBtn">
                    <span id="btnText">{{ __('Resend Verification Email') }}</span>
                    <span id="btnCountdown" class="hidden"></span>
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>

    <script>
        (function() {
            const btn = document.getElementById('resendBtn');
            const btnText = document.getElementById('btnText');
            const btnCountdown = document.getElementById('btnCountdown');
            const storageKey = 'verify_email_cooldown';
            
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
            
            document.getElementById('resendForm').addEventListener('submit', function(e) {
                if (btn.disabled) {
                    e.preventDefault();
                    return;
                }
                startCountdown(30);
            });
        })();
    </script>
</x-guest-layout>
