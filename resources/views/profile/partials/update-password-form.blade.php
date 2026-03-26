<section>
    <header>
        <h2 class="text-lg font-semibold text-cyan-200" style="font-family: 'Space Grotesk', sans-serif;">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-slate-300">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form
        method="post"
        action="{{ route('password.update') }}"
        class="mt-6 space-y-6"
        x-data="{
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
        }"
    >
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-slate-200" />
            <x-password-input id="update_password_current_password" name="current_password" class="mt-1 block w-full rounded-xl border-slate-700 bg-slate-950/60 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:ring-cyan-300/40" autocomplete="current-password" x-model="currentPassword" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-rose-300" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" class="text-slate-200" />
            <x-password-input id="update_password_password" name="password" class="mt-1 block w-full rounded-xl border-slate-700 bg-slate-950/60 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:ring-cyan-300/40" autocomplete="new-password" x-model="newPassword" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-rose-300" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-slate-200" />
            <x-password-input id="update_password_password_confirmation" name="password_confirmation" class="mt-1 block w-full rounded-xl border-slate-700 bg-slate-950/60 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:ring-cyan-300/40" autocomplete="new-password" x-model="confirmPassword" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-rose-300" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button
                x-bind:disabled="!currentPassword && !newPassword && !confirmPassword"
                class="rounded-xl bg-cyan-300 px-4 py-2 text-slate-900 hover:bg-cyan-200 focus:bg-cyan-200 active:bg-cyan-300 focus:ring-cyan-300/70 focus:ring-offset-slate-900 disabled:cursor-not-allowed disabled:opacity-50"
            >{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-300"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
