<x-app-layout>
    <x-slot name="header">
        <div class="overflow-visible flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-cyan-200">{{ config('app.name') }}</p>
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
            selectedAttachmentName: '',
            openImage(url) {
                this.imageUrl = url;
                this.modalOpen = true;
            },
            closeImage() {
                this.modalOpen = false;
                this.imageUrl = '';
            },
            handleAttachmentChange(event) {
                const file = event.target.files?.[0];
                this.selectedAttachmentName = file ? file.name : '';
            },
            clearAttachment() {
                if (this.$refs.attachmentInput) {
                    this.$refs.attachmentInput.value = '';
                }
                this.selectedAttachmentName = '';
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
                            <textarea id="content" name="content" rows="5" maxlength="400" required class="block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40" placeholder="What should we improve as a company?"></textarea>
                            <p class="mt-2 text-xs text-slate-400">Maximum 400 characters.</p>
                            @error('content')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="attachment" class="mb-2 block text-sm font-medium text-slate-200">Image (optional, max {{ config('uploads.post_attachment_max_mb') }} MB)</label>
                            <div class="rounded-xl border border-slate-700 bg-slate-950/60 px-3 py-2">
                                <input x-ref="attachmentInput" @change="handleAttachmentChange($event)" id="attachment" type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp" class="sr-only" />
                                <div class="flex items-center gap-3">
                                    <label for="attachment" class="shrink-0 cursor-pointer rounded-lg bg-cyan-300 px-3 py-1.5 text-xs font-semibold text-slate-900 transition hover:bg-cyan-200">Choose File</label>
                                    <p class="min-w-0 flex-1 truncate text-sm text-slate-200" x-text="selectedAttachmentName || 'No file chosen'"></p>
                                    <button x-show="selectedAttachmentName" x-transition.opacity type="button" @click="clearAttachment()" class="shrink-0 rounded-md border border-white/20 px-2 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10" style="display: none;" aria-label="Clear selected image">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
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
                        <article
                            x-data="{ expanded: false, truncatable: false }"
                            x-init="$nextTick(() => { truncatable = $refs.content.scrollHeight > 112; })"
                            class="rounded-2xl border border-white/10 bg-slate-900/80 p-5 shadow-xl shadow-cyan-950/20"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm sm:text-base lg:text-lg font-semibold text-cyan-200">{{ $post->user->name ?? 'Unknown User' }}</p>
                                    @if (($post->user?->isAdmin()))
                                        <span class="rounded-full border border-amber-300/30 bg-amber-300/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-200">Admin</span>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-400">{{ $post->created_at?->diffForHumans() }}</span>
                            </div>

                            <p
                                x-ref="content"
                                class="mt-3 max-w-full whitespace-pre-line wrap-anywhere text-sm sm:text-base lg:text-lg leading-relaxed text-slate-200"
                                :class="{ 'max-h-28 overflow-hidden': truncatable && !expanded }"
                            >{{ $post->content }}</p>

                            <button
                                x-show="truncatable"
                                x-cloak
                                type="button"
                                @click="expanded = !expanded"
                                class="mt-2 text-xs font-semibold text-cyan-200 underline underline-offset-2 hover:text-cyan-100"
                            >
                                <span x-show="!expanded">See more</span>
                                <span x-show="expanded">See less</span>
                            </button>

                            @if ($post->attachment)
                                <div class="mt-4">
                                    <button
                                        type="button"
                                        @click="openImage('{{ asset('storage/' . $post->attachment) }}')"
                                        class="inline-flex items-center rounded-xl bg-cyan-300 px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-cyan-200"
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
