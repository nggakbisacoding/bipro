<?php

namespace Modules\Keyword\Entities;

use App\Models\JobBatch;
use App\Models\Model;
use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Keyword\Entities\Scopes\KeywordScope;
use Modules\Post\Entities\Post;
use Modules\Project\Entities\Project;

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
 * @property \Illuminate\Support\Carbon|null $last_crawled
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Modules\Auth\Entities\User|null $creator
 * @property-read Post|null $posts
 * @property-read \Modules\Auth\Entities\User|null $updater
 *
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
 *
 * @property \Illuminate\Support\Carbon|null $since
 * @property \Illuminate\Support\Carbon|null $until
 * @property-read string|null $hash_id
 * @property-read string|null $hash_id_raw
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereSince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereUntil($value)
 * @method static \Modules\Keyword\Database\Factories\KeywordFactory factory($count = null, $state = [])
 *
 * @property string|null $batch_id
 * @property-read JobBatch|null $jobBatch
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereBatchId($value)
 *
 * @property-read \Modules\Keyword\Entities\KeywordProfile|null $profile
 * @property int $project_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereLastCrawled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereProjectId($value)
 *
 * @mixin \Eloquent
 */
class Keyword extends Model
{
    use HasHashId, HasHashIdRouting, KeywordScope;

    protected $fillable = [
        'name',
        'source',
        'type',
        'status',
        'is_first',
        'last_post',
        'since',
        'until',
        'batch_id',
        'last_crawled',
        'project_id',
    ];

    protected $casts = [
        'is_first' => 'boolean',
        'last_post' => 'datetime',
        'last_crawled' => 'datetime',
        'since' => 'datetime',
        'until' => 'datetime',
        'status' => 'boolean',
    ];

    public const SOURCE_INSTAGRAM = 'instagram';

    public const SOURCE_YOUTUBE = 'youtube';

    public const SOURCE_TIKTOK = 'tiktok';

    public const SOURCE_FACEBOOK = 'facebook';

    public const SOURCE_TWITTER = 'twitter';

    public const TYPE_KEYWORD = 'keyword';

    public const TYPE_ACCOUNT = 'account';

    /**
     * Retrieve the associated post.
     *
     * @return MorphOne<Post>
     */
    public function posts(): MorphOne
    {
        return $this->morphOne(Post::class, 'postable');
    }

    /**
     * Retrieve the job batch associated with this instance.
     *
     * @return BelongsTo<JobBatch, Keyword>
     */
    public function jobBatch(): BelongsTo
    {
        return $this->belongsTo(JobBatch::class, 'batch_id');
    }

    /**
     * Retrieves the project associated with this model.
     *
     * @return BelongsTo<Project, Keyword>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id')
            ->select(['id', 'name', 'is_complete']);
    }

    /**
     * Creates a new instance of the factory for the PHP function.
     *
     * @return \Modules\Keyword\Database\Factories\KeywordFactory
     */
    protected static function newFactory()
    {
        return \Modules\Keyword\Database\Factories\KeywordFactory::new();
    }
}
