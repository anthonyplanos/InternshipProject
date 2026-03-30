<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes, LogsActivity {
        HasRoles::assignRole as protected assignRoleFromTrait;
        HasRoles::syncRoles as protected syncRolesFromTrait;
        HasRoles::removeRole as protected removeRoleFromTrait;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'pending_email',
        'pending_email_verified_at',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'pending_email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('account')
            ->logOnly(['name', 'email', 'role'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName): string => match ($eventName) {
                'deleted' => $this->buildAccountEventDescription('deactivated'),
                'restored' => $this->buildAccountEventDescription('reactivated'),
                'force_deleted' => 'User account permanently deleted',
                default => "User account {$eventName}",
            });
    }

    protected function buildAccountEventDescription(string $status): string
    {
        $accountName = (string) ($this->name ?? 'Unknown User');
        $accountEmail = (string) ($this->email ?? 'no-email');

        return "User account {$status}: {$accountName} ({$accountEmail})";
    }

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            if (Schema::hasColumn('users', 'role') && empty($user->role)) {
                $user->role = 'User';
            }
        });

        static::deleting(function (self $user): void {
            if ($user->isForceDeleting()) {
                $user->posts()->withTrashed()->forceDelete();

                return;
            }

            $user->posts()->delete();
        });

        static::restoring(function (self $user): void {
            $user->posts()->withTrashed()->restore();
        });

        static::forceDeleted(function (self $user): void {
            activity('account')
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->event('force_deleted')
                ->withProperties([
                    'source' => 'admin_force_delete',
                    'ip' => request()?->ip(),
                    'target_user_id' => $user->id,
                    'target_user_name' => $user->name,
                    'target_user_email' => $user->email,
                ])
                ->log('User account permanently deleted');
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    /**
     * Persist a role snapshot on the users table so it is visible in DB tools.
     */
    public function syncRoleSnapshot(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        $roleName = $this->roles()->orderBy('roles.id')->value('name');

        if ($this->role !== $roleName) {
            $this->forceFill(['role' => $roleName])->saveQuietly();
        }
    }

    public function assignRole(...$roles)
    {
        $result = $this->assignRoleFromTrait(...$roles);
        $this->syncRoleSnapshot();

        return $result;
    }

    public function syncRoles(...$roles)
    {
        $result = $this->syncRolesFromTrait(...$roles);
        $this->syncRoleSnapshot();

        return $result;
    }

    public function removeRole(...$role)
    {
        $result = $this->removeRoleFromTrait(...$role);
        $this->syncRoleSnapshot();

        return $result;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('Staff');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->hasAnyRole(['Admin', 'Staff']);
    }

    public function getPostLoginRedirectPath(): string
    {
        return $this->hasAnyRole(['Admin', 'Staff']) ? '/admin' : '/dashboard';
    }
}
