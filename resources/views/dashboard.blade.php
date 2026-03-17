<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-cyan-200">Company Feed</p>
                <h2 class="mt-1 text-2xl font-bold text-cyan-200" style="font-family: 'Space Grotesk', sans-serif;">Idea Stream</h2>
            </div>
            <span class="rounded-full border border-emerald-300/30 bg-emerald-300/10 px-3 py-1 text-xs font-medium text-emerald-200">Anonymous Mode Active</span>
        </div>
    </x-slot>

    <div
        class="min-h-screen bg-slate-950 pb-12"
        x-data="{
            modalOpen: false,
            imageUrl: '',
            openImage(url) {
                this.imageUrl = url;
                this.modalOpen = true;
            },
            closeImage() {
                this.modalOpen = false;
                this.imageUrl = '';
            }
        }"
        @keydown.escape.window="closeImage()"
    >
        <div class="mx-auto grid w-full max-w-7xl gap-6 px-4 pt-8 sm:px-6 lg:grid-cols-12 lg:px-8">
            <aside class="lg:col-span-3">
                <div class="rounded-2xl border border-white/10 bg-slate-900/80 p-5 text-slate-200 shadow-xl shadow-cyan-950/20">
                    <p class="text-sm font-semibold text-cyan-200">Welcome, {{ Auth::user()->name }}</p>
                    <p class="mt-2 text-sm text-slate-300">This alias is shown publicly. Keep discussions respectful and constructive.</p>
                    <div class="mt-5 space-y-2 text-sm text-slate-300">
                        <p class="rounded-lg bg-white/5 px-3 py-2">Trending: Product Improvements</p>
                        <p class="rounded-lg bg-white/5 px-3 py-2">Trending: Workload Balance</p>
                        <p class="rounded-lg bg-white/5 px-3 py-2">Trending: Team Rituals</p>
                    </div>
                </div>
            </aside>

            <section class="space-y-6 lg:col-span-6">
                @if (session('status'))
                    <div class="rounded-xl border border-emerald-300/30 bg-emerald-300/10 px-4 py-3 text-sm text-emerald-100">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="rounded-2xl border border-white/10 bg-slate-900/80 p-5 shadow-xl shadow-cyan-950/20">
                    <h3 class="text-lg font-semibold text-cyan-200" style="font-family: 'Space Grotesk', sans-serif;">Create a Post</h3>
                    <p class="mt-1 text-sm text-slate-300">Share your idea, concern, or suggestion anonymously.</p>

                    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label for="content" class="mb-2 block text-sm font-medium text-slate-200">Your Post</label>
                            <textarea id="content" name="content" rows="5" maxlength="1200" required class="block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40" placeholder="What should we improve as a company?"></textarea>
                            @error('content')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="attachment" class="mb-2 block text-sm font-medium text-slate-200">Image (optional)</label>
                            <input id="attachment" type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp" class="block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-slate-200 file:me-4 file:rounded-lg file:border-0 file:bg-cyan-300 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-slate-900 hover:file:bg-cyan-200" />
                            @error('attachment')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="rounded-xl bg-cyan-300 px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-cyan-200">
                                Publish Anonymously
                            </button>
                        </div>
                    </form>
                </div>

                <div class="space-y-4">
                    @forelse ($posts as $post)
                        <article class="rounded-2xl border border-white/10 bg-slate-900/80 p-5 shadow-xl shadow-cyan-950/20">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-cyan-200">{{ $post->user->name ?? 'Unknown User' }}</p>
                                    @if (($post->user?->isAdmin()))
                                        <span class="rounded-full border border-amber-300/30 bg-amber-300/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-200">Admin</span>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-400">{{ $post->created_at?->diffForHumans() }}</span>
                            </div>

                            <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-200">{{ $post->content }}</p>

                            @if ($post->attachment)
                                <div class="mt-4">
                                    <button
                                        type="button"
                                        @click="openImage('{{ asset('storage/' . $post->attachment) }}')"
                                        class="inline-flex items-center rounded-lg border border-cyan-300/30 bg-cyan-300/10 px-3 py-1.5 text-xs font-medium text-cyan-100 hover:bg-cyan-300/20"
                                    >
                                        View Image
                                    </button>
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-2xl border border-white/10 bg-slate-900/80 p-5 text-sm text-slate-300 shadow-xl shadow-cyan-950/20">
                            No posts yet. Be the first to share an idea.
                        </div>
                    @endforelse

                    <div>
                        {{ $posts->links() }}
                    </div>
                </div>
            </section>

            <aside class="space-y-5 lg:col-span-3">
                <div class="rounded-2xl border border-white/10 bg-slate-900/80 p-5 shadow-xl shadow-cyan-950/20">
                    <p class="text-sm font-semibold text-cyan-200">Community Health</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-300">
                        <li class="rounded-lg bg-white/5 px-3 py-2">Respectful discourse score: 95%</li>
                        <li class="rounded-lg bg-white/5 px-3 py-2">Posts this week: 48</li>
                        <li class="rounded-lg bg-white/5 px-3 py-2">Open discussions: 16</li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-white/10 bg-slate-900/80 p-5 shadow-xl shadow-cyan-950/20">
                    <p class="text-sm font-semibold text-cyan-200">Posting Tips</p>
                    <ul class="mt-3 list-disc space-y-2 ps-5 text-sm text-slate-300">
                        <li>Describe the issue and why it matters.</li>
                        <li>Suggest one realistic improvement.</li>
                        <li>Use neutral language and concrete examples.</li>
                    </ul>
                </div>
            </aside>
        </div>

        <div
            x-show="modalOpen"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 px-4"
            style="display: none;"
            @click.self="closeImage()"
        >
            <div class="w-full max-w-2xl rounded-2xl border border-white/10 bg-slate-900 p-3 shadow-2xl shadow-cyan-950/40">
                <div class="mb-3 flex items-center justify-between px-2">
                    <p class="text-sm font-semibold text-cyan-200">Image Preview</p>
                    <button
                        type="button"
                        @click="closeImage()"
                        class="rounded-lg border border-white/15 px-3 py-1 text-xs font-medium text-slate-200 hover:bg-white/10"
                    >
                        Close
                    </button>
                </div>

                <div class="max-h-[60vh] overflow-auto rounded-xl border border-white/10 bg-slate-950 p-2">
                    <img :src="imageUrl" alt="Post image preview" class="mx-auto max-h-[50vh] max-w-full rounded-lg object-contain" />
                </div>

                <div class="mt-3 flex items-center justify-end gap-2 px-2 pb-1">
                    <a :href="imageUrl" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-white/15 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-white/10">
                        Open in New Tab
                    </a>
                    <a :href="imageUrl" download class="rounded-lg bg-cyan-300 px-3 py-1.5 text-xs font-semibold text-slate-900 hover:bg-cyan-200">
                        Download
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
