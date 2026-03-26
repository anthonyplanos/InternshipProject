@props(['disabled' => false])

<div x-data="{ show: false }" class="relative">
    <input
        type="password"
        @disabled($disabled)
        {{ $attributes->class(['pr-11']) }}
        x-bind:type="show ? 'text' : 'password'"
    >

    <button
        type="button"
        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 transition hover:text-slate-300 focus:outline-none"
        x-on:click="show = !show"
        x-bind:aria-label="show ? 'Hide password' : 'Show password'"
        x-bind:title="show ? 'Hide password' : 'Show password'"
    >
        <span class="sr-only" x-text="show ? 'Hide password' : 'Show password'"></span>

        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10 3c-4.5 0-8.03 2.94-9.54 6.98a1 1 0 0 0 0 .7C1.97 14.72 5.5 17.66 10 17.66s8.03-2.94 9.54-6.98a1 1 0 0 0 0-.7C18.03 5.94 14.5 3 10 3Zm0 12.66c-3.52 0-6.4-2.23-7.72-5.33C3.6 7.23 6.48 5 10 5s6.4 2.23 7.72 5.33C16.4 13.43 13.52 15.66 10 15.66Z" />
            <path d="M10 7a3.33 3.33 0 1 0 0 6.66A3.33 3.33 0 0 0 10 7Zm0 4.66a1.33 1.33 0 1 1 0-2.66 1.33 1.33 0 0 1 0 2.66Z" />
        </svg>

        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M3.28 2.22a.75.75 0 1 0-1.06 1.06l2.06 2.06A10.75 10.75 0 0 0 .46 9.98a1 1 0 0 0 0 .7c1.5 4.04 5.03 6.98 9.54 6.98 1.82 0 3.5-.48 4.97-1.3l1.75 1.76a.75.75 0 1 0 1.06-1.06L3.28 2.22Zm10.2 12.32A7.95 7.95 0 0 1 10 15.66c-3.52 0-6.4-2.23-7.72-5.33a8.7 8.7 0 0 1 3.38-3.9l1.55 1.55A3.3 3.3 0 0 0 6.67 10c0 1.84 1.5 3.33 3.33 3.33.72 0 1.39-.22 1.93-.6l1.55 1.55Z" />
            <path d="M10 5c3.52 0 6.4 2.23 7.72 5.33a8.73 8.73 0 0 1-1.96 2.89.75.75 0 1 0 1.06 1.06 10.78 10.78 0 0 0 2.72-3.6 1 1 0 0 0 0-.7C18.03 5.94 14.5 3 10 3c-1.24 0-2.43.22-3.52.62a.75.75 0 0 0 .52 1.4C7.9 4.67 8.92 4.5 10 4.5Z" />
            <path d="M10 8.17a.75.75 0 0 0-.75.75c0 .2.08.39.22.53l1.08 1.08a.75.75 0 0 0 .53.22.75.75 0 0 0 .75-.75A1.83 1.83 0 0 0 10 8.17Z" />
        </svg>
    </button>
</div>