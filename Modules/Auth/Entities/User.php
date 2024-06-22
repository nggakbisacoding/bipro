<?php

namespace Modules\Auth\Entities;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laragear\TwoFactor\Contracts\TwoFactorAuthenticatable;
use Laragear\TwoFactor\TwoFactorAuthentication;
use Laravel\Sanctum\HasApiTokens;
use Modules\Auth\Database\Factories\UserFactory;
use Modules\Auth\Entities\Traits\Attribute\UserAttribute;
use Modules\Auth\Entities\Traits\Method\UserMethod;
use Modules\Auth\Entities\Traits\Relationship\UserRelationship;
use Modules\Auth\Entities\Traits\Scope\UserScope;
use Modules\Auth\Notifications\Frontend\ResetPasswordNotification;
use Modules\Auth\Notifications\Frontend\VerifyEmail;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Modules\Auth\Entities\User
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $password_changed_at
 * @property bool $active
 * @property string|null $timezone
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property bool $to_be_logged_out
 * @property string|null $provider
 * @property string|null $provider_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $avatar
 * @property-read string $permissions_label
 * @property-read string $roles_label
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Auth\Entities\PasswordHistory> $passwordHistories
 * @property-read int|null $password_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Laragear\TwoFactor\Models\TwoFactorAuthentication $twoFactorAuth
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder|User allAccess()
 * @method static \Illuminate\Database\Eloquent\Builder|User byType($type)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyActive()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyDeactivated()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User search($term)
 * @method static \Illuminate\Database\Eloquent\Builder|User users()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePasswordChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereToBeLoggedOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Project\Entities\Project> $projects
 * @property-read int|null $projects_count
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail, TwoFactorAuthenticatable
{
    use HasApiTokens,
        HasFactory,
        HasRoles,
        Impersonate,
        MustVerifyEmailTrait,
        Notifiable,
        QueryCacheable,
        SoftDeletes,
        TwoFactorAuthentication,
        UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope;

    public $cacheFor = 86400;

    protected static $flushCacheOnUpdate = true;

    public const TYPE_ADMIN = 'admin';

    public const TYPE_USER = 'user';

    protected $fillable = [
        'type',
        'name',
        'email',
        'email_verified_at',
        'password',
        'password_changed_at',
        'active',
        'timezone',
        'last_login_at',
        'last_login_ip',
        'to_be_logged_out',
        'provider',
        'provider_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'last_login_at',
        'email_verified_at',
        'password_changed_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'to_be_logged_out' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'avatar',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'permissions',
        'roles',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the registration verification email.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Return true or false if the user can impersonate an other user.
     */
    public function canImpersonate(): bool
    {
        return $this->can('admin.access.user.impersonate');
    }

    /**
     * Return true or false if the user can be impersonate.
     */
    public function canBeImpersonated(): bool
    {
        return ! $this->isMasterAdmin();
    }

    /**
     * Retrieves the projects associated with this model.
     *
     * @return HasMany<\Modules\Project\Entities\Project> The projects associated with this model.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(\Modules\Project\Entities\Project::class, 'created_by')
            ->select('id', 'name')
            ->when(! $this->isAdmin() || ! $this->isMasterAdmin(), function ($query) {
                return $query->where('created_by', $this->id);
            })
            ->orderBy('created_at', 'desc');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
