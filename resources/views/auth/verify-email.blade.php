<x-guest-layout>
    <div>
        <p class="text-sm uppercase tracking-[0.2em] text-cyan-300">Employee Access</p>
        <h2 class="mt-3 text-3xl font-bold text-cyan-200" style="font-family: 'Space Grotesk', sans-serif;">Verify your email</h2>
        <p class="mt-2 text-sm text-slate-300">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking the link we just emailed to you? If you did not receive the email, we will gladly send another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-6 rounded-xl border border-emerald-300/30 bg-emerald-300/10 px-4 py-3 text-sm font-medium text-emerald-100">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    @if (session('verification_error'))
        <div class="mt-4 rounded-xl border border-rose-300/30 bg-rose-300/10 px-4 py-3 text-sm font-medium text-rose-200">
            {{ session('verification_error') }}
        </div>
    @endif

    <div class="mt-8 space-y-4">
        <form method="POST" action="{{ route('verification.send') }}" class="w-full">
            @csrf

            <div class="w-full">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-cyan-300 px-4 py-3 text-sm font-semibold text-slate-900 transition hover:bg-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-300/70 focus:ring-offset-2 focus:ring-offset-slate-900">
                    {{ __('Resend Verification Email') }}
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf

            <button type="submit" class="text-sm font-medium text-cyan-300 transition hover:text-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-300/70 focus:ring-offset-2 focus:ring-offset-slate-900 rounded-md">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
