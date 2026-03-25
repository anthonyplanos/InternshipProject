<section class="space-y-6">
    <header>
        <h2 class="text-lg font-semibold text-cyan-200" style="font-family: 'Space Grotesk', sans-serif;">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-slate-300">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        class="rounded-xl border border-rose-400/40 bg-rose-500/20 text-rose-200 hover:bg-rose-500/30 focus:ring-rose-300/60 focus:ring-offset-slate-900"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="rounded-2xl border border-white/10 bg-slate-900/95 p-6 text-slate-100">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-cyan-200" style="font-family: 'Space Grotesk', sans-serif;">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-slate-300">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4 rounded-xl border-slate-700 bg-slate-950/60 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:ring-cyan-300/40"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-rose-300" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl border border-white/15 bg-slate-800/80 text-slate-100 hover:bg-slate-700/80 focus:ring-cyan-300/60 focus:ring-offset-slate-900">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3 rounded-xl border border-rose-400/40 bg-rose-500/20 text-rose-200 hover:bg-rose-500/30 focus:ring-rose-300/60 focus:ring-offset-slate-900">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
