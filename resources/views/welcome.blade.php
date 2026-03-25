<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'ShoreTalks') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700|space-grotesk:500,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100" style="font-family: 'Outfit', sans-serif;">
        <div class="relative overflow-hidden">
            <div class="pointer-events-none absolute -left-28 top-0 h-72 w-72 rounded-full bg-cyan-400/25 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-20 top-40 h-80 w-80 rounded-full bg-emerald-400/20 blur-3xl"></div>

            <header class="relative z-10 mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-6 lg:px-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-cyan-300/40 bg-cyan-300/10 text-cyan-200">ST</span>
                    <span class="text-lg font-bold tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">ShoreTalks</span>
                </a>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-xl border border-cyan-300/40 bg-cyan-300/10 px-4 py-2 text-sm font-medium text-cyan-100 hover:bg-cyan-300/20">Open Feed</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-xl border border-white/20 px-4 py-2 text-sm font-medium text-slate-200 hover:bg-white/10">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-xl bg-cyan-300 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-cyan-200">Register</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <main class="relative z-10 mx-auto grid w-full max-w-7xl items-center gap-12 px-6 pb-14 pt-10 lg:grid-cols-2 lg:px-10 lg:pb-20">
                <section>
                    <p class="inline-flex rounded-full border border-cyan-300/30 bg-cyan-300/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-200">Internal Collaboration Platform</p>
                    <h1 class="mt-6 text-4xl font-bold leading-tight text-cyan-200 sm:text-5xl" style="font-family: 'Space Grotesk', sans-serif;">
                        Anonymous ideas.
                        <br>
                        Better workplace decisions.
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-relaxed text-slate-300">
                        ShoreTalks gives employees a safe internal space to post ideas, ask hard questions, and discuss proposals without social pressure.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('register') }}" class="rounded-2xl bg-cyan-300 px-6 py-3 text-sm font-semibold text-slate-900 hover:bg-cyan-200">Create Account</a>
                        <a href="{{ route('login') }}" class="rounded-2xl border border-white/20 px-6 py-3 text-sm font-medium text-slate-100 hover:bg-white/10">Sign In</a>
                    </div>
                </section>

                <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-cyan-950/20 backdrop-blur sm:p-8">
                    <h2 class="text-xl font-bold text-cyan-200" style="font-family: 'Space Grotesk', sans-serif;">What employees can do</h2>

                    <div class="mt-6 space-y-4">
                        <article class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-sm font-semibold text-cyan-200">Post anonymously</p>
                            <p class="mt-1 text-sm text-slate-300">Share insights, blockers, and product ideas without exposing your identity.</p>
                        </article>
                        <article class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-sm font-semibold text-emerald-200">Build healthy discussions</p>
                            <p class="mt-1 text-sm text-slate-300">Teams can upvote, comment, and align around clear suggestions.</p>
                        </article>
                        <article class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-sm font-semibold text-amber-200">Track what matters</p>
                            <p class="mt-1 text-sm text-slate-300">Spot trending themes and move strong ideas into action faster.</p>
                        </article>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
