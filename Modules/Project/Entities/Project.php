<?php

namespace Modules\Project\Entities;

use App\Models\Model;
use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Keyword\Entities\Keyword;

/**
 * Modules\Project\Entities\Project
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $user_id
 * @property bool $is_active
 * @property bool $is_complete
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Auth\Entities\User|null $creator
 * @property-read \Modules\Auth\Entities\User|null $updater
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Model createdBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model updatedBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereIsComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUserId($value)
 *
 * @property-read string|null $hash_id
 * @property-read string $hash_id_raw
 *
 * @mixin \Eloquent
 */
class Project extends Model
{
    use HasHashId, HasHashIdRouting;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'is_active',
        'is_complete',
    ];

    /**
     * Retrieves the keywords associated with this instance.
     *
     * @return HasMany<Keyword>
     */
    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class)
            ->select(['id', 'name', 'source', 'created_at'])
            ->orderByDesc('created_at');
    }
}
