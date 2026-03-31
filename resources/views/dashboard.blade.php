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
            createContent: '',
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
            },
            clearCreatePost() {
                this.createContent = '';
                if (this.$refs.createPostContent) {
                    this.$refs.createPostContent.value = '';
                }
                this.clearAttachment();
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
                            <textarea x-ref="createPostContent" x-model="createContent" id="content" name="content" rows="5" maxlength="400" required class="block w-full resize-none rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40" placeholder="What should we improve as a company?"></textarea>
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

                        <div class="flex justify-end gap-2">
                            <button
                                type="button"
                                x-show="createContent.trim().length > 0"
                                x-transition.opacity
                                @click="clearCreatePost()"
                                class="rounded-xl border border-white/20 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10"
                                style="display: none;"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="rounded-xl bg-cyan-300 px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-cyan-200">
                                Publish Anonymously
                            </button>
                        </div>
                    </form>
                </div>

                <div class="space-y-4">
                    @forelse ($posts as $post)
                        @php
                            $initialComments = $post->comments->map(fn ($comment) => [
                                'id' => $comment->id,
                                'parent_id' => null,
                                'user_id' => $comment->user_id,
                                'author' => (string) ($comment->user?->name ?? 'Deactivated User'),
                                'content' => (string) $comment->content,
                                'created_at_human' => (string) ($comment->created_at?->diffForHumans() ?? 'just now'),
                                'replies' => $comment->replies->map(fn ($reply) => [
                                    'id' => $reply->id,
                                    'parent_id' => $comment->id,
                                    'user_id' => $reply->user_id,
                                    'author' => (string) ($reply->user?->name ?? 'Deactivated User'),
                                    'content' => (string) $reply->content,
                                    'created_at_human' => (string) ($reply->created_at?->diffForHumans() ?? 'just now'),
                                ])->values(),
                            ])->values();
                        @endphp
                        <article
                            x-data="{
                                expanded: false,
                                truncatable: false,
                                menuOpen: false,
                                editing: false,
                                editContent: @js($post->content),
                                originalContent: @js($post->content),
                                commentsModalOpen: false,
                                commentContent: '',
                                commentError: '',
                                isSubmittingComment: false,
                                currentUserId: @js((int) Auth::id()),
                                replyDrafts: {},
                                replyFormsOpen: {},
                                replyErrors: {},
                                replyingTo: null,
                                replyVisibleCount: {},
                                commentMenusOpen: {},
                                replyMenusOpen: {},
                                comments: @js($initialComments),
                                commentUrl: @js(route('posts.comments.store', $post)),
                                replyUrlTemplate: @js(route('comments.replies.store', ['comment' => '__COMMENT_ID__'])),
                                deleteUrlTemplate: @js(route('comments.destroy', ['comment' => '__COMMENT_ID__'])),
                                getReplyUrl(commentId) {
                                    return this.replyUrlTemplate.replace('__COMMENT_ID__', String(commentId));
                                },
                                getDeleteUrl(commentId) {
                                    return this.deleteUrlTemplate.replace('__COMMENT_ID__', String(commentId));
                                },
                                async submitComment() {
                                    const content = this.commentContent.trim();

                                    if (!content) {
                                        this.commentError = 'Comment cannot be empty.';
                                        return;
                                    }

                                    this.isSubmittingComment = true;
                                    this.commentError = '';

                                    try {
                                        const formData = new FormData();
                                        formData.append('_token', @js(csrf_token()));
                                        formData.append('comment_content', content);

                                        const response = await fetch(this.commentUrl, {
                                            method: 'POST',
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                            },
                                            body: formData,
                                        });

                                        const payload = await response.json().catch(() => ({}));

                                        if (!response.ok) {
                                            this.commentError = payload?.errors?.comment_content?.[0]
                                                ?? payload?.message
                                                ?? 'Unable to post comment right now.';
                                            return;
                                        }

                                        this.comments.push(payload.comment);
                                        this.commentContent = '';
                                    } catch (error) {
                                        this.commentError = 'Unable to post comment right now.';
                                    } finally {
                                        this.isSubmittingComment = false;
                                    }
                                }
                                ,
                                async submitReply(commentId) {
                                    const replyBody = (this.replyDrafts[commentId] ?? '').trim();

                                    if (!replyBody) {
                                        this.replyErrors[commentId] = 'Reply cannot be empty.';
                                        return;
                                    }

                                    this.replyingTo = commentId;
                                    this.replyErrors[commentId] = '';

                                    try {
                                        const formData = new FormData();
                                        formData.append('_token', @js(csrf_token()));
                                        formData.append('reply_content', replyBody);

                                        const response = await fetch(this.getReplyUrl(commentId), {
                                            method: 'POST',
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                            },
                                            body: formData,
                                        });

                                        const payload = await response.json().catch(() => ({}));

                                        if (!response.ok) {
                                            this.replyErrors[commentId] = payload?.errors?.reply_content?.[0]
                                                ?? payload?.message
                                                ?? 'Unable to post reply right now.';
                                            return;
                                        }

                                        const parent = this.comments.find((comment) => Number(comment.id) === Number(commentId));

                                        if (parent) {
                                            if (!Array.isArray(parent.replies)) {
                                                parent.replies = [];
                                            }

                                            parent.replies.push(payload.reply);
                                        }

                                        this.replyDrafts[commentId] = '';
                                        this.replyFormsOpen[commentId] = false;
                                    } catch (error) {
                                        this.replyErrors[commentId] = 'Unable to post reply right now.';
                                    } finally {
                                        this.replyingTo = null;
                                    }
                                },
                                async deleteComment(commentId) {
                                    if (!confirm('Are you sure you want to delete this comment?')) {
                                        return;
                                    }

                                    try {
                                        const response = await fetch(this.getDeleteUrl(commentId), {
                                            method: 'DELETE',
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'X-CSRF-TOKEN': @js(csrf_token()),
                                            },
                                        });

                                        const payload = await response.json().catch(() => ({}));

                                        if (!response.ok) {
                                            this.commentError = payload?.message ?? 'Unable to delete comment right now.';
                                            return;
                                        }

                                        this.comments = this.comments.filter((comment) => Number(comment.id) !== Number(commentId));
                                    } catch (error) {
                                        this.commentError = 'Unable to delete comment right now.';
                                    }
                                },
                                async deleteReply(commentId, replyId) {
                                    if (!confirm('Are you sure you want to delete this reply?')) {
                                        return;
                                    }

                                    try {
                                        const response = await fetch(this.getDeleteUrl(replyId), {
                                            method: 'DELETE',
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'X-CSRF-TOKEN': @js(csrf_token()),
                                            },
                                        });

                                        const payload = await response.json().catch(() => ({}));

                                        if (!response.ok) {
                                            this.replyErrors[commentId] = payload?.message ?? 'Unable to delete reply right now.';
                                            return;
                                        }

                                        const parent = this.comments.find((comment) => Number(comment.id) === Number(commentId));

                                        if (!parent || !Array.isArray(parent.replies)) {
                                            return;
                                        }

                                        parent.replies = parent.replies.filter((reply) => Number(reply.id) !== Number(replyId));
                                    } catch (error) {
                                        this.replyErrors[commentId] = 'Unable to delete reply right now.';
                                    }
                                }
                            }"
                            x-init="$nextTick(() => { truncatable = $refs.content.scrollHeight > 112; })"
                            class="rounded-2xl border border-white/10 bg-slate-900/80 p-5 shadow-xl shadow-cyan-950/20"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm sm:text-base lg:text-lg font-semibold text-cyan-200">{{ $post->user->name ?? 'Deactivated User' }}</p>
                                    @if (($post->user?->isAdmin()))
                                        <span class="rounded-full border border-amber-300/30 bg-amber-300/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-200">Admin</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-slate-400">{{ $post->created_at?->diffForHumans() }}</span>

                                    @if ((int) $post->user_id === (int) Auth::id())
                                        <div class="relative" @click.outside="menuOpen = false">
                                            <button
                                                type="button"
                                                @click="menuOpen = !menuOpen"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-full text-slate-300/90 transition hover:bg-white/10 hover:text-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-300/60"
                                                aria-label="Post options"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4.5 w-4.5" aria-hidden="true">
                                                    <path d="M10 4.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm0 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm0 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" />
                                                </svg>
                                            </button>

                                            <div
                                                x-show="menuOpen"
                                                x-transition.opacity
                                                class="absolute right-0 z-20 mt-2 w-36 rounded-xl border border-white/10 bg-slate-900/95 p-1.5 shadow-xl shadow-cyan-950/30"
                                                style="display: none;"
                                            >
                                                <button
                                                    type="button"
                                                    @click="editing = true; menuOpen = false; $nextTick(() => $refs.editContent.focus())"
                                                    class="w-full rounded-lg px-3 py-2 text-left text-xs font-semibold text-cyan-200 transition hover:bg-white/10"
                                                >
                                                    Edit
                                                </button>

                                                <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-xs font-semibold text-rose-200 transition hover:bg-rose-500/20">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <template x-if="!editing">
                                <div>
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
                                </div>
                            </template>

                            @if ((int) $post->user_id === (int) Auth::id())
                                <form
                                    x-show="editing"
                                    x-cloak
                                    method="POST"
                                    action="{{ route('posts.update', $post) }}"
                                    class="mt-4 space-y-3"
                                    style="display: none;"
                                >
                                    @csrf
                                    @method('PUT')

                                    <textarea
                                        x-ref="editContent"
                                        x-model="editContent"
                                        name="edit_content"
                                        rows="4"
                                        maxlength="400"
                                        required
                                        class="block w-full resize-none rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40"
                                    ></textarea>

                                    @error('edit_content')
                                        <p class="text-sm text-rose-300">{{ $message }}</p>
                                    @enderror

                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            @click="editing = false; editContent = originalContent"
                                            class="rounded-lg border border-white/20 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            class="rounded-lg bg-cyan-300 px-3 py-1.5 text-xs font-semibold text-slate-900 transition hover:bg-cyan-200"
                                        >
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            @endif

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

                            <div class="mt-5 border-t border-white/10 pt-4">
                                <p class="text-sm font-semibold text-cyan-200">Comments (<span x-text="comments.length"></span>)</p>

                                <div class="mt-3 space-y-3">
                                    <template x-for="comment in comments.slice(-3)" :key="comment.id">
                                        <div class="rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-xs font-semibold text-cyan-200" x-text="comment.author"></p>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] text-slate-400" x-text="comment.created_at_human"></span>
                                                    <div class="relative" x-show="Number(comment.user_id) === Number(currentUserId)" @click.outside="commentMenusOpen[comment.id] = false">
                                                        <button
                                                            type="button"
                                                            @click="commentMenusOpen[comment.id] = !(commentMenusOpen[comment.id] ?? false)"
                                                            class="inline-flex h-6 w-6 items-center justify-center rounded-full text-slate-300/90 transition hover:bg-white/10 hover:text-cyan-200"
                                                            aria-label="Comment options"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                                                                <path d="M10 4.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm0 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm0 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" />
                                                            </svg>
                                                        </button>

                                                        <div
                                                            x-show="commentMenusOpen[comment.id] ?? false"
                                                            x-transition.opacity
                                                            class="absolute right-0 z-20 mt-1 w-28 rounded-lg border border-white/10 bg-slate-900/95 p-1.5 shadow-xl shadow-cyan-950/30"
                                                            style="display: none;"
                                                        >
                                                            <button
                                                                type="button"
                                                                @click="commentMenusOpen[comment.id] = false; deleteComment(comment.id)"
                                                                class="w-full rounded-md px-2 py-1.5 text-left text-[11px] font-semibold text-rose-200 transition hover:bg-rose-500/20"
                                                            >
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="mt-1 whitespace-pre-line wrap-anywhere text-sm text-slate-200" x-text="comment.content"></p>

                                            <div class="mt-2 space-y-2 border-l border-white/10 ps-3" x-show="Array.isArray(comment.replies) && comment.replies.length > 0">
                                                <template x-for="reply in comment.replies.slice(0, replyVisibleCount[comment.id] ?? 2)" :key="'inline-reply-' + reply.id">
                                                    <div class="rounded-lg border border-white/10 bg-slate-900/60 px-2.5 py-2">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <p class="text-[11px] font-semibold text-cyan-200" x-text="reply.author"></p>
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-[10px] text-slate-400" x-text="reply.created_at_human"></span>
                                                                <div class="relative" x-show="Number(reply.user_id) === Number(currentUserId)" @click.outside="replyMenusOpen[reply.id] = false">
                                                                    <button
                                                                        type="button"
                                                                        @click="replyMenusOpen[reply.id] = !(replyMenusOpen[reply.id] ?? false)"
                                                                        class="inline-flex h-5 w-5 items-center justify-center rounded-full text-slate-300/90 transition hover:bg-white/10 hover:text-cyan-200"
                                                                        aria-label="Reply options"
                                                                    >
                                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5" aria-hidden="true">
                                                                            <path d="M10 4.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm0 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm0 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" />
                                                                        </svg>
                                                                    </button>

                                                                    <div
                                                                        x-show="replyMenusOpen[reply.id] ?? false"
                                                                        x-transition.opacity
                                                                        class="absolute right-0 z-20 mt-1 w-28 rounded-lg border border-white/10 bg-slate-900/95 p-1.5 shadow-xl shadow-cyan-950/30"
                                                                        style="display: none;"
                                                                    >
                                                                        <button
                                                                            type="button"
                                                                            @click="replyMenusOpen[reply.id] = false; deleteReply(comment.id, reply.id)"
                                                                            class="w-full rounded-md px-2 py-1.5 text-left text-[11px] font-semibold text-rose-200 transition hover:bg-rose-500/20"
                                                                        >
                                                                            Delete
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="mt-1 whitespace-pre-line wrap-anywhere text-xs text-slate-200" x-text="reply.content"></p>
                                                    </div>
                                                </template>

                                                <div class="pt-1" x-show="comment.replies.length > 2">
                                                    <button
                                                        type="button"
                                                        @click="replyVisibleCount[comment.id] = (replyVisibleCount[comment.id] ?? 2) >= comment.replies.length ? 2 : comment.replies.length"
                                                        class="text-[11px] font-semibold text-cyan-200 underline underline-offset-2 hover:text-cyan-100"
                                                    >
                                                        <span x-text="(replyVisibleCount[comment.id] ?? 2) >= comment.replies.length ? 'See less replies' : 'See more replies'"></span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mt-2 flex justify-end">
                                                <button
                                                    type="button"
                                                    @click="replyFormsOpen[comment.id] = !(replyFormsOpen[comment.id] ?? false); if (!(replyFormsOpen[comment.id] ?? false)) { replyErrors[comment.id] = ''; }"
                                                    class="rounded-md border border-cyan-300/30 px-2 py-0.5 text-[11px] font-semibold text-cyan-200 transition hover:bg-cyan-300/10"
                                                >
                                                    <span x-text="(replyFormsOpen[comment.id] ?? false) ? 'Cancel reply' : 'Reply'"></span>
                                                </button>
                                            </div>

                                            <form class="mt-2 space-y-2" x-show="replyFormsOpen[comment.id] ?? false" style="display: none;" @submit.prevent="submitReply(comment.id)">
                                                <textarea
                                                    x-model="replyDrafts[comment.id]"
                                                    rows="2"
                                                    maxlength="400"
                                                    required
                                                    class="block w-full resize-none rounded-lg border border-slate-700 bg-slate-950/60 px-2.5 py-2 text-xs text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40"
                                                    placeholder="Reply to this comment..."
                                                ></textarea>

                                                <p x-show="replyErrors[comment.id]" x-text="replyErrors[comment.id]" class="text-xs text-rose-300"></p>

                                                <div class="flex justify-end">
                                                    <button :disabled="replyingTo === comment.id" type="submit" class="rounded-lg bg-cyan-300 px-2.5 py-1 text-[11px] font-semibold text-slate-900 transition hover:bg-cyan-200 disabled:cursor-not-allowed disabled:opacity-60">
                                                        <span x-show="replyingTo !== comment.id">Reply</span>
                                                        <span x-show="replyingTo === comment.id">Posting...</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </template>

                                    <p x-show="comments.length === 0" class="text-sm text-slate-400">No comments yet. Start the conversation.</p>
                                </div>

                                <div class="mt-2" x-show="comments.length > 3">
                                    <button
                                        type="button"
                                        @click="commentsModalOpen = true"
                                        class="text-xs font-semibold text-cyan-200 underline underline-offset-2 hover:text-cyan-100"
                                    >
                                        See more comments
                                    </button>
                                </div>

                                <form @submit.prevent="submitComment()" class="mt-3 space-y-2">
                                    @csrf
                                    <textarea
                                        x-model="commentContent"
                                        name="comment_content"
                                        rows="2"
                                        maxlength="400"
                                        required
                                        class="block w-full resize-none rounded-xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40"
                                        placeholder="Write a comment..."
                                    ></textarea>

                                    <p x-show="commentError" x-text="commentError" class="text-sm text-rose-300"></p>

                                    <div class="flex justify-end">
                                        <button :disabled="isSubmittingComment" type="submit" class="rounded-lg bg-cyan-300 px-3 py-1.5 text-xs font-semibold text-slate-900 transition hover:bg-cyan-200 disabled:cursor-not-allowed disabled:opacity-60">
                                            <span x-show="!isSubmittingComment">Comment</span>
                                            <span x-show="isSubmittingComment">Posting...</span>
                                        </button>
                                    </div>
                                </form>

                                <div
                                    x-show="commentsModalOpen"
                                    x-transition.opacity
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 px-4"
                                    style="display: none;"
                                    @click.self="commentsModalOpen = false"
                                    @keydown.escape.window="commentsModalOpen = false"
                                >
                                    <div class="w-full max-w-2xl rounded-2xl border border-white/10 bg-slate-900 p-3 shadow-2xl shadow-cyan-950/40">
                                        <div class="mb-3 flex items-center justify-between px-2">
                                            <p class="text-sm font-semibold text-cyan-200">All Comments (<span x-text="comments.length"></span>)</p>
                                            <button
                                                type="button"
                                                @click="commentsModalOpen = false"
                                                class="rounded-lg border border-white/15 px-3 py-1 text-xs font-medium text-slate-200 hover:bg-white/10"
                                            >
                                                Close
                                            </button>
                                        </div>

                                        <div class="max-h-[60vh] space-y-3 overflow-auto rounded-xl border border-white/10 bg-slate-950 p-3">
                                            <template x-for="comment in comments" :key="'modal-' + comment.id">
                                                <div class="rounded-xl border border-white/10 bg-slate-900/70 px-3 py-2.5">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <p class="text-xs font-semibold text-cyan-200" x-text="comment.author"></p>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-[11px] text-slate-400" x-text="comment.created_at_human"></span>
                                                            <div class="relative" x-show="Number(comment.user_id) === Number(currentUserId)" @click.outside="commentMenusOpen['modal-' + comment.id] = false">
                                                                <button
                                                                    type="button"
                                                                    @click="commentMenusOpen['modal-' + comment.id] = !(commentMenusOpen['modal-' + comment.id] ?? false)"
                                                                    class="inline-flex h-6 w-6 items-center justify-center rounded-full text-slate-300/90 transition hover:bg-white/10 hover:text-cyan-200"
                                                                    aria-label="Comment options"
                                                                >
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                                                                        <path d="M10 4.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm0 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm0 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" />
                                                                    </svg>
                                                                </button>

                                                                <div
                                                                    x-show="commentMenusOpen['modal-' + comment.id] ?? false"
                                                                    x-transition.opacity
                                                                    class="absolute right-0 z-20 mt-1 w-28 rounded-lg border border-white/10 bg-slate-900/95 p-1.5 shadow-xl shadow-cyan-950/30"
                                                                    style="display: none;"
                                                                >
                                                                    <button
                                                                        type="button"
                                                                        @click="commentMenusOpen['modal-' + comment.id] = false; deleteComment(comment.id)"
                                                                        class="w-full rounded-md px-2 py-1.5 text-left text-[11px] font-semibold text-rose-200 transition hover:bg-rose-500/20"
                                                                    >
                                                                        Delete
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="mt-1 whitespace-pre-line wrap-anywhere text-sm text-slate-200" x-text="comment.content"></p>

                                                    <div class="mt-2 space-y-2 ps-3 border-l border-white/10" x-show="Array.isArray(comment.replies) && comment.replies.length > 0">
                                                                        <template x-for="reply in comment.replies.slice(0, replyVisibleCount[comment.id] ?? 3)" :key="'reply-' + reply.id">
                                                            <div class="rounded-lg border border-white/10 bg-slate-950/70 px-2.5 py-2">
                                                                <div class="flex items-center justify-between gap-2">
                                                                    <p class="text-[11px] font-semibold text-cyan-200" x-text="reply.author"></p>
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="text-[10px] text-slate-400" x-text="reply.created_at_human"></span>
                                                                        <div class="relative" x-show="Number(reply.user_id) === Number(currentUserId)" @click.outside="replyMenusOpen['modal-' + reply.id] = false">
                                                                            <button
                                                                                type="button"
                                                                                @click="replyMenusOpen['modal-' + reply.id] = !(replyMenusOpen['modal-' + reply.id] ?? false)"
                                                                                class="inline-flex h-5 w-5 items-center justify-center rounded-full text-slate-300/90 transition hover:bg-white/10 hover:text-cyan-200"
                                                                                aria-label="Reply options"
                                                                            >
                                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5" aria-hidden="true">
                                                                                    <path d="M10 4.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm0 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm0 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" />
                                                                                </svg>
                                                                            </button>

                                                                            <div
                                                                                x-show="replyMenusOpen['modal-' + reply.id] ?? false"
                                                                                x-transition.opacity
                                                                                class="absolute right-0 z-20 mt-1 w-28 rounded-lg border border-white/10 bg-slate-900/95 p-1.5 shadow-xl shadow-cyan-950/30"
                                                                                style="display: none;"
                                                                            >
                                                                                <button
                                                                                    type="button"
                                                                                    @click="replyMenusOpen['modal-' + reply.id] = false; deleteReply(comment.id, reply.id)"
                                                                                    class="w-full rounded-md px-2 py-1.5 text-left text-[11px] font-semibold text-rose-200 transition hover:bg-rose-500/20"
                                                                                >
                                                                                    Delete
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <p class="mt-1 whitespace-pre-line wrap-anywhere text-xs text-slate-200" x-text="reply.content"></p>
                                                            </div>
                                                        </template>

                                                                        <div class="pt-1" x-show="comment.replies.length > 3">
                                                                            <button
                                                                                type="button"
                                                                                @click="replyVisibleCount[comment.id] = (replyVisibleCount[comment.id] ?? 3) >= comment.replies.length ? 3 : comment.replies.length"
                                                                                class="text-[11px] font-semibold text-cyan-200 underline underline-offset-2 hover:text-cyan-100"
                                                                            >
                                                                                <span x-text="(replyVisibleCount[comment.id] ?? 3) >= comment.replies.length ? 'See less replies' : 'See more replies'"></span>
                                                                            </button>
                                                                        </div>
                                                    </div>

                                                    <div class="mt-2 flex justify-end">
                                                        <button
                                                            type="button"
                                                            @click="replyFormsOpen[comment.id] = !(replyFormsOpen[comment.id] ?? false); if (!(replyFormsOpen[comment.id] ?? false)) { replyErrors[comment.id] = ''; }"
                                                            class="rounded-md border border-cyan-300/30 px-2 py-0.5 text-[11px] font-semibold text-cyan-200 transition hover:bg-cyan-300/10"
                                                        >
                                                            <span x-text="(replyFormsOpen[comment.id] ?? false) ? 'Cancel reply' : 'Reply'"></span>
                                                        </button>
                                                    </div>

                                                    <form class="mt-2 space-y-2" x-show="replyFormsOpen[comment.id] ?? false" style="display: none;" @submit.prevent="submitReply(comment.id)">
                                                        <textarea
                                                            x-model="replyDrafts[comment.id]"
                                                            rows="2"
                                                            maxlength="400"
                                                            required
                                                            class="block w-full resize-none rounded-lg border border-slate-700 bg-slate-950/60 px-2.5 py-2 text-xs text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40"
                                                            placeholder="Reply to this comment..."
                                                        ></textarea>

                                                        <p x-show="replyErrors[comment.id]" x-text="replyErrors[comment.id]" class="text-xs text-rose-300"></p>

                                                        <div class="flex justify-end">
                                                            <button :disabled="replyingTo === comment.id" type="submit" class="rounded-lg bg-cyan-300 px-2.5 py-1 text-[11px] font-semibold text-slate-900 transition hover:bg-cyan-200 disabled:cursor-not-allowed disabled:opacity-60">
                                                                <span x-show="replyingTo !== comment.id">Reply</span>
                                                                <span x-show="replyingTo === comment.id">Posting...</span>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
