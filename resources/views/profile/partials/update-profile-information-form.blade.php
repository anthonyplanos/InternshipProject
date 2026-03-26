<section>
    @php
        $currentEmail = (string) $user->email;
        $pendingEmail = (string) ($user->pending_email ?? '');
        $pendingVerified = (bool) $user->pending_email_verified_at;
    @endphp

    <header>
        <h2 class="text-lg font-semibold text-cyan-200" style="font-family: 'Space Grotesk', sans-serif;">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-slate-300">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <div
        class="mt-6 space-y-6"
        x-data="{
            email: @js($pendingVerified && $pendingEmail ? $pendingEmail : $currentEmail),
            currentEmail: @js($currentEmail),
            pendingEmail: @js($pendingEmail),
            pendingVerified: @js($pendingVerified),
        }"
    >
        <!-- Hidden verification form -->
        <form id="send-new-email-verification" method="post" action="{{ route('profile.email.verification.send') }}">
            @csrf
            <input type="hidden" name="new_email" x-model="email" />
        </form>

        <!-- Hidden verification link form -->
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <!-- Profile Update Form -->
        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('patch')

            <div>
                <x-input-label for="name" :value="__('Name')" class="text-slate-200" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl border-slate-700 bg-slate-950/60 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:ring-cyan-300/40" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2 text-rose-300" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-slate-200" />
                <div class="mt-1">
                    <x-text-input 
                        id="email" 
                        name="email" 
                        type="email" 
                        class="block w-full rounded-xl border-slate-700 bg-slate-950/60 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:ring-cyan-300/40" 
                        x-model="email" 
                        :placeholder="$currentEmail" 
                        required 
                        autocomplete="username" 
                    />
                </div>

                <x-input-error class="mt-2 text-rose-300" :messages="$errors->get('email')" />
                <x-input-error class="mt-2 text-rose-300" :messages="$errors->get('new_email')" />

                @if ($pendingEmail)
                    <p class="mt-2 text-sm text-amber-400">
                        {{ __('Verification email sent to') }} <strong>{{ $pendingEmail }}</strong>
                    </p>
                @endif

                @if (session('status') === 'pending-email-verification-link-sent')
                    <p class="mt-2 text-sm font-medium text-emerald-300">
                        {{ __('Verification link sent to your new email. Open it, then come back and save.') }}
                    </p>
                @endif

                @if (session('status') === 'pending-email-verified')
                    <p class="mt-2 text-sm font-medium text-emerald-300">
                        {{ __('New email verified. You can now click Save.') }}
                    </p>
                @endif

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="mt-2 text-sm text-slate-200">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification" class="rounded-md text-sm font-medium text-cyan-300 underline hover:text-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-300/70 focus:ring-offset-2 focus:ring-offset-slate-900">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm font-medium text-emerald-300">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <div class="flex flex-row items-center gap-2">
                    <x-secondary-button
                        type="submit"
                        form="send-new-email-verification"
                        x-bind:disabled="!email || email.toLowerCase() === currentEmail.toLowerCase()"
                        class="rounded-xl border-cyan-300/40 bg-cyan-300/10 px-3 py-2 text-cyan-100 whitespace-nowrap hover:bg-cyan-300/20 focus:ring-cyan-300/70 focus:ring-offset-slate-900 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ __('Verify New Email') }}
                    </x-secondary-button>

                    <x-primary-button
                        x-bind:disabled="pendingEmail && !pendingVerified"
                        class="rounded-xl bg-cyan-600 px-3 py-2 text-white whitespace-nowrap hover:bg-cyan-700 focus:ring-cyan-300/70 focus:ring-offset-slate-900 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ __('Save') }}
                    </x-primary-button>
                </div>

                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        @click="show = false"
                        class="text-sm font-medium text-emerald-400 cursor-pointer transition"
                    >
                        {{ __('Saved.') }}
                    </p>
                @endif
            </div>
        </form>
    </div>
</section>
