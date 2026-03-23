<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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

class User extends Authenticatable implements FilamentUser
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
            ->setDescriptionForEvent(fn (string $eventName): string => "User account {$eventName}");
    }

    protected static function booted(): void
    {
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
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
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

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->isAdmin();
    }

    public function getPostLoginRedirectPath(): string
    {
        return $this->isAdmin() ? '/admin' : '/dashboard';
    }
}
