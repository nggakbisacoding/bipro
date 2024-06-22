<?php

namespace Modules\Post\Entities;

use App\Models\Model;
use App\Models\Traits\ClearsResponseCache;
use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Keyword\Entities\Keyword;
use Modules\Post\Entities\Traits\Scope\PostScope;

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
 *
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
 *
 * @property-read string|null $hash_id
 * @property-read string|null $hash_id_raw
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Post searchByHashtag($term)
 *
 * @property string|null $avatar
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereAvatar($value)
 *
 * @mixin \Eloquent
 */
class Post extends Model
{
    use ClearsResponseCache, HasHashId, PostScope;

    protected $fillable = [
        'post_id',
        'name',
        'username',
        'message',
        'hashtags',
        'date',
        'stats',
        'postable_type',
        'postable_id',
        'avatar',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
        'stats' => 'array',
    ];

    /**
     * Retrieves the postable relation.
     *
     * @return MorphTo<Keyword, Post> The postable relation.
     */
    public function postable()
    {
        /**
         * @var MorphTo<Keyword, Post> $relation
         */
        $relation = $this->morphTo();

        return $relation->withTrashed();
    }

    /**
     * Retrieves all the media associated with this post.
     *
     * @return HasMany<PostMedia>
     */
    public function medias()
    {
        return $this->hasMany(PostMedia::class);
    }
}
