<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Post extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id',
        'category',
        'category_id',
        'content',
        'attachment',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('post')
            ->logOnly(['user_id', 'category', 'category_id', 'content', 'attachment'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName): string => match ($eventName) {
                'created' => 'Post created: ' . trim((string) $this->content),
                default => "Post {$eventName}",
            });
    }

    public function categoryRecord(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id')->withTrashed();
    }

    public function authorDisplayName(): string
    {
        $author = $this->user;

        if (! $author || $author->trashed()) {
            return 'Deactivated User';
        }

        return (string) $author->name;
    }

    public function comments(): HasMany
    {
        return $this->hasMany(\App\Models\Comment::class)
            ->whereNull('parent_id')
            ->oldest('created_at');
    }
}
