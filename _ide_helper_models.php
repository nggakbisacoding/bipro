<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\JobBatch
 *
 * @property string $id
 * @property string $name
 * @property int $total_jobs
 * @property int $pending_jobs
 * @property int $failed_jobs
 * @property string $failed_job_ids
 * @property \Illuminate\Support\Collection|null $options
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch whereFailedJobIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch whereFailedJobs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch wherePendingJobs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobBatch whereTotalJobs($value)
 * @mixin \Eloquent
 */
	class JobBatch extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\JobStatus
 *
 * @method static \Illuminate\Database\Eloquent\Builder|JobStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobStatus query()
 */
	class JobStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Model
 *
 * @property-read \Modules\Auth\Entities\User|null $creator
 * @property-read \Modules\Auth\Entities\User|null $updater
 * @method static \Illuminate\Database\Eloquent\Builder|Model createdBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model updatedBy($userId)
 * @mixin \Eloquent
 */
	class Model extends \Eloquent {}
}

namespace Modules\Auth\Entities{
/**
 * Modules\Auth\Entities\PasswordHistory
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class PasswordHistory extends \Eloquent {}
}

namespace Modules\Auth\Entities{
/**
 * Modules\Auth\Entities\Permission
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $module
 * @property string $guard_name
 * @property string|null $description
 * @property int|null $parent_id
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $children
 * @property-read int|null $children_count
 * @property-read Permission|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SpatiePermission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Auth\Entities\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Permission isChild()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission isMaster()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission isParent()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission singular()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Permission extends \Eloquent {}
}

namespace Modules\Auth\Entities{
/**
 * Modules\Auth\Entities\Role
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $permissions_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Auth\Entities\User> $users
 * @property-read int|null $users_count
 * @method static \Modules\Auth\Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role search($term)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Role extends \Eloquent {}
}

namespace Modules\Auth\Entities{
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
 * @mixin \Eloquent
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail, \Laragear\TwoFactor\Contracts\TwoFactorAuthenticatable {}
}

namespace Modules\Keyword\Entities{
/**
 * Modules\Keyword\Entities\Keyword
 *
 * @property int $id
 * @property string $name
 * @property string $source
 * @property string $type
 * @property bool $status
 * @property bool $is_first
 * @property \Illuminate\Support\Carbon|null $last_post
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Modules\Auth\Entities\User|null $creator
 * @property-read Post|null $posts
 * @property-read \Modules\Auth\Entities\User|null $updater
 * @method static \Illuminate\Database\Eloquent\Builder|Model createdBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword search($term)
 * @method static \Illuminate\Database\Eloquent\Builder|Model updatedBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereIsFirst($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereLastPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword withoutTrashed()
 * @property \Illuminate\Support\Carbon|null $since
 * @property \Illuminate\Support\Carbon|null $until
 * @property-read string|null $hash_id
 * @property-read string|null $hash_id_raw
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereSince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereUntil($value)
 * @method static \Modules\Keyword\Database\Factories\KeywordFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 * @property string|null $batch_id
 * @property-read \App\Models\JobBatch|null $jobBatch
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereBatchId($value)
 */
	class Keyword extends \Eloquent {}
}

namespace Modules\Post\Entities{
/**
 * Modules\Post\Entities\Post
 *
 * @property int $id
 * @property string $postable_type
 * @property int $postable_id
 * @property string $post_id
 * @property string $name
 * @property string $username
 * @property string $message
 * @property string $hashtags
 * @property \Illuminate\Support\Carbon $date
 * @property array $stats
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Post\Entities\PostMedia> $medias
 * @property-read int|null $medias_count
 * @property-read \Modules\Keyword\Entities\Keyword $postable
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post search($term)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereHashtags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUsername($value)
 * @property-read string|null $hash_id
 * @property-read string|null $hash_id_raw
 * @method static \Illuminate\Database\Eloquent\Builder|Post searchByHashtag($term)
 * @mixin \Eloquent
 */
	class Post extends \Eloquent {}
}

namespace Modules\Post\Entities{
/**
 * Modules\Post\Entities\PostExport
 *
 * @property int $id
 * @property string|null $batch_id
 * @property string $name
 * @property string|null $path
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Auth\Entities\User|null $creator
 * @property-read string|null $hash_id
 * @property-read string|null $hash_id_raw
 * @property-read JobBatch|null $jobBatch
 * @property-read \Modules\Auth\Entities\User|null $updater
 * @method static \Illuminate\Database\Eloquent\Builder|Model createdBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model updatedBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport whereBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostExport whereUpdatedBy($value)
 * @mixin \Eloquent
 */
	class PostExport extends \Eloquent {}
}

namespace Modules\Post\Entities{
/**
 * Modules\Post\Entities\PostMedia
 *
 * @property int $id
 * @property int $post_id
 * @property string $type
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Post\Entities\Post $post
 * @method static \Illuminate\Database\Eloquent\Builder|PostMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostMedia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostMedia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostMedia wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostMedia wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostMedia whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostMedia whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class PostMedia extends \Eloquent {}
}

