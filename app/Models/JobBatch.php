<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
class JobBatch extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_batches';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $casts = [
        'options' => 'collection',
        'failed_jobs' => 'integer',
        'created_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /**
     * Get the total number of jobs that have been processed by the batch thus far.
     *
     * @return int
     */
    public function processedJobs()
    {
        return $this->total_jobs - $this->pending_jobs;
    }

    /**
     * Get the percentage of jobs that have been processed (between 0-100).
     */
    public function progress(): int
    {
        return $this->total_jobs > 0 ? round(($this->processedJobs() / $this->total_jobs) * 100) : 0;
    }

    /**
     * Determine if the batch has pending jobs
     */
    public function hasPendingJobs(): bool
    {
        return $this->pending_jobs > 0;
    }

    /**
     * Determine if the batch has finished executing.
     */
    public function finished(): bool
    {
        return ! is_null($this->finished_at);
    }

    /**
     * Determine if the batch has job failures.
     */
    public function hasFailures(): bool
    {
        return $this->failed_jobs > 0;
    }

    /**
     * Determine if all jobs failed.
     */
    public function failed(): bool
    {
        return $this->failed_jobs === $this->total_jobs;
    }

    /**
     * Determine if the batch has been canceled.
     */
    public function cancelled(): bool
    {
        return ! is_null($this->cancelled_at);
    }
}
