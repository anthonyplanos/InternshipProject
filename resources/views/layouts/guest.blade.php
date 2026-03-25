<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CollabHub') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700|space-grotesk:500,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-slate-950 text-slate-100" style="font-family: 'Outfit', sans-serif;">
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-cyan-400/30 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-emerald-300/30 blur-3xl"></div>

            <div class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid w-full overflow-hidden rounded-3xl border border-white/10 bg-slate-900/80 shadow-2xl shadow-cyan-900/30 backdrop-blur xl:grid-cols-2">
                    <section class="hidden border-r border-white/10 bg-gradient-to-br from-cyan-500/15 via-slate-900 to-emerald-500/10 p-10 xl:block">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-cyan-300/40 bg-cyan-300/10 text-lg font-bold text-cyan-200">ST</span>
                            <span class="text-xl font-bold tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">ShoreTalks</span>
                        </a>

                        <h1 class="mt-12 text-4xl font-bold leading-tight text-white" style="font-family: 'Space Grotesk', sans-serif;">
                            Speak Freely.
                            <br>
                            Build Better Together.
                        </h1>
                        <p class="mt-6 max-w-md text-base leading-relaxed text-slate-300">
                            Internal space for ideas, questions, and respectful debate. Share your perspective anonymously and help your team move faster.
                        </p>

                        <div class="mt-10 space-y-4">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-sm font-semibold text-cyan-200">Anonymous by design</p>
                                <p class="mt-1 text-sm text-slate-300">Posts show your alias, not your employee identity.</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-sm font-semibold text-emerald-200">Healthy collaboration</p>
                                <p class="mt-1 text-sm text-slate-300">Constructive dialogue, thoughtful feedback, and transparent decisions.</p>
                            </div>
                        </div>
                    </section>

                    <section class="p-6 sm:p-10">
                        <div class="mx-auto w-full max-w-md">
                            {{ $slot }}
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>
