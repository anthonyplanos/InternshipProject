<?php

use App\Models\Post;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/admin/login', '/login');
Route::redirect('/admin/register', '/register');

Route::get('/dashboard', function (Request $request) {
    if ($request->user()->isAdmin()) {
        return redirect('/admin');
    }

    $posts = Post::query()
        ->with('user')
        ->latest()
        ->paginate(10);

    return view('dashboard', compact('posts'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/posts', function (Request $request) {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:200'],
            'attachment' => ['nullable', 'image', 'max:' . config('uploads.post_attachment_max_kb'), 'mimes:jpg,jpeg,png,gif,webp'],
        ]);

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('post-attachments', 'public')
            : null;

        Post::create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'attachment' => $attachmentPath,
        ]);

        return back()->with('status', 'Post published successfully.');
    })->name('posts.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
