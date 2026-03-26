<x-guest-layout>
    <div>
        <p class="text-sm uppercase tracking-[0.2em] text-cyan-300">Employee Access</p>
        <h2 class="mt-3 text-3xl font-bold text-cyan-200" style="font-family: 'Space Grotesk', sans-serif;">Sign in to {{ config('app.name') }}</h2>
        <p class="mt-2 text-sm text-slate-300">Use your work email to access the anonymous discussion platform.</p>
    </div>

    <x-auth-session-status class="mt-6 rounded-xl border border-emerald-300/30 bg-emerald-300/10 px-4 py-3 text-sm text-emerald-100" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-slate-200">Work Email</label>
            <input id="email" class="block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-rose-300" />
        </div>

        <div>
            <label for="password" class="mb-2 block text-sm font-medium text-slate-200">Password</label>
            <input id="password" class="block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-rose-300" />
        </div>

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center text-sm text-slate-300">
                <input id="remember_me" type="checkbox" class="rounded border-slate-600 bg-slate-900 text-cyan-300 shadow-sm focus:ring-cyan-300" name="remember">
                <span class="ms-2">Keep me signed in</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-cyan-300 transition hover:text-cyan-200" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <div class="space-y-4 pt-2">
            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-cyan-300 px-4 py-3 text-sm font-semibold text-slate-900 transition hover:bg-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-300/70 focus:ring-offset-2 focus:ring-offset-slate-900">
                Enter Workspace
            </button>

            <p class="text-center text-sm text-slate-300">
                New employee?
                <a href="{{ route('register') }}" class="font-semibold text-cyan-300 hover:text-cyan-200">Create your account</a>
            </p>
        </div>
    </form>
</x-guest-layout>
