<?php

namespace App\Models;

use App\Models\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Rennokki\QueryCache\Traits\QueryCacheable;
use RichanFongdasen\EloquentBlameable\BlameableTrait;

/**
 * App\Models\Model
 *
 * @property-read \Modules\Auth\Entities\User|null $creator
 * @property-read \Modules\Auth\Entities\User|null $updater
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Model createdBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model updatedBy($userId)
 *
 * @mixin \Eloquent
 */
class Model extends BaseModel
{
    use BlameableTrait, ClearsResponseCache, HasFactory, QueryCacheable;

    protected static $logFillable = true;

    protected static $logName = 'default';

    public $cacheFor = 86400;

    protected static $flushCacheOnUpdate = true;

    public $cacheDriver = 'redis';
}
