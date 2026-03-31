<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Comment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'post_id',
        'parent_id',
        'user_id',
        'content',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('comment')
            ->logOnly(['post_id', 'parent_id', 'user_id', 'content'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName): string => match ($eventName) {
                'created' => $this->buildCreatedDescription(),
                'updated' => $this->buildUpdatedDescription(),
                'deleted' => $this->buildDeletedDescription(),
                default => $this->buildGenericDescription($eventName),
            });
    }

    protected function buildCreatedDescription(): string
    {
        $contentPreview = Str::limit(trim((string) $this->content), 220);

        if (filled($this->parent_id)) {
            return "Reply added to comment #{$this->parent_id} on Post #{$this->post_id}: {$contentPreview}";
        }

        return "Comment added on Post #{$this->post_id}: {$contentPreview}";
    }

    protected function buildUpdatedDescription(): string
    {
        $contentPreview = Str::limit(trim((string) $this->content), 220);

        if (filled($this->parent_id)) {
            return "Reply #{$this->id} on comment #{$this->parent_id} (Post #{$this->post_id}): {$contentPreview}";
        }

        return "Comment #{$this->id} on Post #{$this->post_id}: {$contentPreview}";
    }

    protected function buildDeletedDescription(): string
    {
        if (filled($this->parent_id)) {
            return "Reply #{$this->id} on comment #{$this->parent_id} (Post #{$this->post_id})";
        }

        return "Comment #{$this->id} on Post #{$this->post_id}";
    }

    protected function buildGenericDescription(string $eventName): string
    {
        $event = Str::headline($eventName);

        if (filled($this->parent_id)) {
            return "Reply #{$this->id} on comment #{$this->parent_id} (Post #{$this->post_id}) {$event}";
        }

        return "Comment #{$this->id} on Post #{$this->post_id} {$event}";
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->oldest('created_at');
    }
}
