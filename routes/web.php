<?php

use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/admin/login', '/login');
Route::redirect('/admin/register', '/register');

Route::get('/dashboard', function (Request $request) {
    if ($request->user()->hasAnyRole(['Admin', 'Staff'])) {
        return redirect('/admin');
    }

    $search = trim((string) $request->query('search', ''));

    $posts = Post::query()
        ->with(['user', 'categoryRecord', 'comments.user', 'comments.replies.user'])
        ->when($search !== '', function ($query) use ($search) {
            $needle = '%' . strtolower($search) . '%';

            $query->where(function ($innerQuery) use ($needle) {
                $innerQuery
                    ->whereRaw('LOWER(posts.category) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(posts.content) LIKE ?', [$needle])
                    ->orWhereHas('categoryRecord', fn ($categoryQuery) => $categoryQuery->whereRaw('LOWER(name) LIKE ?', [$needle]));
            });
        })
        ->latest()
        ->paginate(10);

    $posts->appends(['search' => $search !== '' ? $search : null]);

    return view('dashboard', compact('posts', 'search'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/posts', function (Request $request) {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:20'],
            'content' => ['required', 'string', 'max:400'],
            'attachment' => ['nullable', 'image', 'max:' . config('uploads.post_attachment_max_kb'), 'mimes:jpg,jpeg,png,gif,webp'],
        ]);

        $categoryName = trim((string) $validated['category']);

        $category = Category::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($categoryName)])
            ->first();

        if (! $category) {
            $category = Category::create([
                'name' => $categoryName,
            ]);
        }

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('post-attachments', 'public')
            : null;

        Post::create([
            'user_id' => $request->user()->id,
            'category' => $categoryName,
            'category_id' => $category->id,
            'content' => $validated['content'],
            'attachment' => $attachmentPath,
        ]);

        return back()->with('status', 'Post published successfully.');
    })->name('posts.store');

    Route::put('/posts/{post}', function (Request $request, Post $post) {
        abort_unless((int) $post->user_id === (int) $request->user()->id, 403);

        $validated = $request->validate([
            'edit_category' => ['required', 'string', 'max:20'],
            'edit_content' => ['required', 'string', 'max:400'],
        ]);

        $categoryName = trim((string) $validated['edit_category']);

        $category = Category::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($categoryName)])
            ->first();

        if (! $category) {
            $category = Category::create([
                'name' => $categoryName,
            ]);
        }

        $post->update([
            'category' => $categoryName,
            'category_id' => $category->id,
            'content' => $validated['edit_content'],
        ]);

        return back()->with('status', 'Post updated successfully.');
    })->name('posts.update');

    Route::post('/posts/{post}/comments', function (Request $request, Post $post) {
        $validated = $request->validate([
            'comment_content' => ['required', 'string', 'max:400'],
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'content' => $validated['comment_content'],
        ]);

        if ($request->expectsJson()) {
            $comment->load('user');

            return response()->json([
                'message' => 'Comment posted successfully.',
                'comment' => [
                    'id' => $comment->id,
                    'parent_id' => null,
                    'user_id' => $comment->user_id,
                    'author' => (string) ($comment->user?->name ?? 'Unknown User'),
                    'content' => (string) $comment->content,
                    'created_at_human' => (string) ($comment->created_at?->diffForHumans() ?? 'just now'),
                    'replies' => [],
                ],
            ]);
        }

        return back()->with('status', 'Comment posted successfully.');
    })->name('posts.comments.store');

    Route::post('/comments/{comment}/replies', function (Request $request, Comment $comment) {
        $validated = $request->validate([
            'reply_content' => ['required', 'string', 'max:400'],
        ]);

        $reply = Comment::create([
            'post_id' => $comment->post_id,
            'parent_id' => $comment->id,
            'user_id' => $request->user()->id,
            'content' => $validated['reply_content'],
        ]);

        if ($request->expectsJson()) {
            $reply->load('user');

            return response()->json([
                'message' => 'Reply posted successfully.',
                'reply' => [
                    'id' => $reply->id,
                    'parent_id' => $comment->id,
                    'user_id' => $reply->user_id,
                    'author' => (string) ($reply->user?->name ?? 'Unknown User'),
                    'content' => (string) $reply->content,
                    'created_at_human' => (string) ($reply->created_at?->diffForHumans() ?? 'just now'),
                ],
            ]);
        }

        return back()->with('status', 'Reply posted successfully.');
    })->name('comments.replies.store');

    Route::delete('/comments/{comment}', function (Request $request, Comment $comment) {
        abort_unless((int) $comment->user_id === (int) $request->user()->id, 403);

        $deletedCommentId = $comment->id;
        $deletedParentId = $comment->parent_id;
        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Comment deleted successfully.',
                'deleted' => [
                    'id' => $deletedCommentId,
                    'parent_id' => $deletedParentId,
                ],
            ]);
        }

        return back()->with('status', 'Comment deleted successfully.');
    })->name('comments.destroy');

    Route::delete('/posts/{post}', function (Request $request, Post $post) {
        abort_unless((int) $post->user_id === (int) $request->user()->id, 403);

        // Soft delete keeps the record for audit trails and activity logs.
        $post->delete();

        return back()->with('status', 'Post deleted successfully.');
    })->name('posts.destroy');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/email/verification-notification', [ProfileController::class, 'sendPendingEmailVerification'])
        ->middleware('throttle:6,1')
        ->name('profile.email.verification.send');
    Route::get('/profile/email/verify/{id}/{hash}', [ProfileController::class, 'verifyPendingEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('profile.email.verify');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
