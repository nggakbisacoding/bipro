<?php

namespace Modules\Post\Entities;

use App\Models\JobBatch;
use App\Models\Model;
use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
class PostExport extends Model
{
    use HasHashId;

    protected $fillable = [
        'name',
        'path',
        'batch_id',
    ];

    protected $with = [
        'jobBatch',
    ];

    /**
     * Retrieve the job batch associated with this instance.
     */
    public function jobBatch(): BelongsTo
    {
        return $this->belongsTo(JobBatch::class, 'batch_id');
    }
}
