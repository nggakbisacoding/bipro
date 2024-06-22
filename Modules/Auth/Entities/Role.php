<?php

namespace Modules\Auth\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\Database\Factories\RoleFactory;
use Modules\Auth\Entities\Traits\Attribute\RoleAttribute;
use Modules\Auth\Entities\Traits\Method\RoleMethod;
use Modules\Auth\Entities\Traits\Scope\RoleScope;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\Permission\Models\Role as SpatieRole;

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
 *
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
 *
 * @mixin \Eloquent
 */
class Role extends SpatieRole
{
    use HasFactory,
        QueryCacheable,
        RoleAttribute,
        RoleMethod,
        RoleScope;

    public $cacheFor = 86400;

    protected static $flushCacheOnUpdate = true;

    /**
     * @var string[]
     */
    protected $with = [
        'permissions',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return RoleFactory::new();
    }
}
