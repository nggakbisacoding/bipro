<?php

namespace Modules\Post\Entities;

use App\Models\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;

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
class PostMedia extends Model
{
    use ClearsResponseCache;

    protected $table = 'post_medias';

    protected $fillable = [
        'post_id',
        'type',
        'path',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
