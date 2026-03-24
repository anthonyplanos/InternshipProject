<x-guest-layout>
    <div>
        <p class="text-sm uppercase tracking-[0.2em] text-cyan-300">Onboarding</p>
        <h2 class="mt-3 text-3xl font-bold text-cyan-200" style="font-family: 'Space Grotesk', sans-serif;">Create your employee account</h2>
        <p class="mt-2 text-sm text-slate-300">Set your public alias for anonymous discussions inside your company workspace.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-slate-200">Public Alias</label>
            <input id="name" class="block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="nickname" placeholder="IdeaNavigator" />
            <p class="mt-2 text-xs text-slate-400">This is how you appear in discussions. Avoid using your real name.</p>
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-rose-300" />
        </div>

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-slate-200">Work Email</label>
            <input id="email" class="block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-rose-300" />
        </div>

        <div>
            <label for="password" class="mb-2 block text-sm font-medium text-slate-200">Password</label>
            <input id="password" class="block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40" type="password" name="password" required autocomplete="new-password" placeholder="Create a secure password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-rose-300" />
        </div>

        <div>
            <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-200">Confirm Password</label>
            <input id="password_confirmation" class="block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-rose-300" />
        </div>

        <div class="rounded-2xl border border-slate-700 bg-slate-950/40 p-4">
            <label for="acknowledge_policy" class="inline-flex items-start gap-3 text-sm text-slate-300">
                <input id="acknowledge_policy" name="acknowledge_policy" type="checkbox" value="1" class="mt-1 rounded border-slate-600 bg-slate-900 text-cyan-300 focus:ring-cyan-300" {{ old('acknowledge_policy') ? 'checked' : '' }} required>
                <span>I understand this platform is for respectful, constructive, and anonymous collaboration inside the company.</span>
            </label>
            <x-input-error :messages="$errors->get('acknowledge_policy')" class="mt-2 text-sm text-rose-300" />
        </div>

        <div class="space-y-4 pt-2">
            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-cyan-300 px-4 py-3 text-sm font-semibold text-slate-900 transition hover:bg-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-300/70 focus:ring-offset-2 focus:ring-offset-slate-900">
                Create Account
            </button>

            <p class="text-center text-sm text-slate-300">
                Already have access?
                <a href="{{ route('login') }}" class="font-semibold text-cyan-300 hover:text-cyan-200">Sign in</a>
            </p>
        </div>
    </form>
</x-guest-layout>
