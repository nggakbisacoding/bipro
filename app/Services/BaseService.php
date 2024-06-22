<?php

namespace App\Services;

/**
 * Class BaseRepository.
 *
 * Modified from: https://github.com/kylenoland/laravel-base-repository
 */
abstract class BaseService
{
    /**
     * The repository model.
     */
    protected $model;

    /**
     * The query builder.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * Alias for the query limit.
     *
     * @var int|null
     */
    protected $take;

    /**
     * Alias for the query limit.
     *
     * @var int|null
     */
    protected $skip;

    /**
     * Alias for the query select.
     *
     * @var string|array
     */
    protected $select;

    /**
     * Alias for the query select.
     *
     * @var string|null
     */
    protected $selectRaw;

    /**
     * Array of related models to eager load.
     *
     * @var array
     */
    protected $with = [];

    /**
     * Array of related models to eager load.
     *
     * @var array
     */
    protected $whereHas = [];

    /**
     * Array of related models to eager load.
     *
     * @var array
     */
    protected $whereHasMorph = [];

    /**
     * Array of related models to eager load count.
     *
     * @var array
     */
    protected $withCount = [];

    /**
     * Array of one or more where clause parameters.
     *
     * @var array
     */
    protected $wheres = [];

    /**
     * Array of one or more where in clause parameters.
     *
     * @var array
     */
    protected $whereIns = [];

    /**
     * Array of one or more where between date parameters.
     *
     * @var array
     */
    protected $whereBetween = [];

    /**
     * Array of one or more when clause parameters.
     *
     * @var array
     */
    protected $when = [];

    /**
     * Array of one or more ORDER BY column/value pairs.
     *
     * @var array
     */
    protected $orderBys = [];

    /**
     * Array of scope methods to call on the model.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * Get all the model records in the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $columns = ['*'])
    {
        $this->newQuery()->eagerLoad();

        $models = $this->query->get($columns);

        $this->unsetClauses();

        return $models;
    }

    /**
     * Count the number of specified model records in the database.
     *
     * @return int
     */
    public function count()
    {
        return $this->get()->count();
    }

    /**
     * Get the first specified model record from the database.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function first()
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $model = $this->query->first();

        $this->unsetClauses();

        return $model;
    }

    /**
     * Get the first specified model record from the database or throw an exception if not found.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function firstOrFail()
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $model = $this->query->firstOrFail();

        $this->unsetClauses();

        return $model;
    }

    /**
     * Get all the specified model records in the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get()
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query->get();

        $this->unsetClauses();

        return $models;
    }

    /**
     * Executes a chunk operation on the query and returns the resulting models.
     *
     * @param  int  $chunkCount The number of models to retrieve per chunk.
     * @param  callable|null  $callback An optional callback function to be executed after each chunk is retrieved.
     * @return bool The resulting models.
     */
    public function chunk(int $chunkCount, callable $callback = null)
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query->chunk($chunkCount, $callback);

        $this->unsetClauses();

        return $models;
    }

    /**
     * Get the specified model record from the database.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById($id)
    {
        $this->unsetClauses();

        $this->newQuery()->eagerLoad();

        return $this->query->findOrFail($id);
    }

    public function getByHashId(string $hashId)
    {
        $this->unsetClauses();

        $this->newQuery()->eagerLoad();

        return $this->query->findByHashId($hashId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByColumn($item, $column, array $columns = ['*'])
    {
        $this->unsetClauses();

        $this->newQuery()->eagerLoad();

        return $this->query->where($column, $item)->first($columns);
    }

    /**
     * Delete the specified model record from the database.
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    public function deleteById($id)
    {
        $this->unsetClauses();

        return $this->getById($id)->delete();
    }

    /**
     * Set the query limit.
     *
     * @param  int  $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->take = $limit;

        return $this;
    }

    /**
     * Set the query offset.
     *
     * @param  int  $offset
     * @return $this
     */
    public function skip($offset)
    {
        $this->skip = $offset;

        return $this;
    }

    /**
     * Set an ORDER BY clause.
     *
     * @param  string  $column
     * @param  string  $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->orderBys[] = compact('column', 'direction');

        return $this;
    }

    /**
     * @param  int  $limit
     * @param  string  $pageName
     * @param  null  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($limit = 25, array $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query->paginate($limit, $columns, $pageName, $page);

        $this->unsetClauses();

        return $models;
    }

    /**
     * Add a simple where clause to the query.
     *
     * @param  string|array  $column
     * @return $this
     */
    public function select($column = ['*'])
    {
        $this->select = $column;

        return $this;
    }

    /**
     * Add a simple where clause to the query.
     *
     * @param  string  $query
     * @return $this
     */
    public function selectRaw($query)
    {
        $this->selectRaw = $query;

        return $this;
    }

    /**
     * Add a simple where clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @param  string  $operator
     * @return $this
     */
    public function where($column, $value, $operator = '=')
    {
        $this->wheres[] = compact('column', 'value', 'operator');

        return $this;
    }

    /**
     * Add a simple where clause to the query.
     *
     * @return $this
     */
    public function whereRaw(string $query, array $parameters)
    {
        $this->wheres[] = compact('query', 'parameters');

        return $this;
    }

    /**
     * Add a simple where clause to the query.
     *
     * @param  string  $relations
     * @return $this
     */
    public function whereHas($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $this->whereHas = $relations;

        return $this;
    }

    /**
     * Add a simple where clause to the query.
     *
     * @param  string  $relations
     * @return $this
     */
    public function whereHasMorph($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $this->whereHasMorph = $relations;

        return $this;
    }

    /**
     * Add a simple where in clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $values
     * @return $this
     */
    public function whereIn($column, $values)
    {
        $values = is_array($values) ? $values : [$values];

        $this->whereIns[] = compact('column', 'values');

        return $this;
    }

    /**
     * Add a where between clause to the query.
     *
     * @param  string  $column
     * @param  mixed  $values
     * @return $this
     */
    public function whereBetween($column, $values)
    {
        $values = is_array($values) ? $values : [$values];

        $this->whereBetween[] = compact('column', 'values');

        return $this;
    }

    /**
     * Set Eloquent relationships to eager load.
     *
     * @return $this
     */
    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $this->with = $relations;

        return $this;
    }

    /**
     * Set Eloquent relationships to count.
     *
     * @return $this
     */
    public function withCount($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $this->withCount = $relations;

        return $this;
    }

    /**
     * Adds a condition and callback to the "when" array.
     *
     * @param  ?bool  $condition The condition that needs to be met.
     * @param  ?callable  $callback The callback function to be executed.
     * @return $this The current instance of the class.
     */
    public function when(?bool $condition, $callback = null)
    {
        $this->when[] = compact('condition', 'callback');

        return $this;
    }

    public function getAll($orderBy = 'created_at', $orderDirection = 'desc')
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $pageSize = request('limit', 10);
        $orderDirection = request('order', $orderDirection);
        $orderField = request('field', $orderBy);
        $orderField = str_replace(',', '->', $orderField);

        /** @var \Modules\Auth\Entities\User $user */
        $user = auth()->user();

        $models = $this->query
            // @phpstan-ignore-next-line
            ->when(request('q'), fn ($query, $term) => $query->search($term)) // @phpstan-ignore-line
            ->when($user->isUser(), fn ($query) => $query->where('created_by', '=', $user->id))
            ->orderBy($orderField, $orderDirection === 'ascend' ? 'ASC' : 'DESC')
            ->paginate($pageSize);

        $this->unsetClauses();

        return $models;
    }

    public function getRandom($limit = 10)
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query->inRandomOrder()->limit($limit)->get();

        $this->unsetClauses();

        return $models;
    }

    public function toSql()
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query->toSql();

        $this->unsetClauses();

        return $models;
    }

    /**
     * Create a new instance of the model's query builder.
     *
     * @return $this
     */
    protected function newQuery()
    {
        $this->query = $this->model->newQuery();

        return $this;
    }

    /**
     * Add relationships to the query builder to eager load.
     *
     * @return $this
     */
    protected function eagerLoad()
    {

        if ($this->whereHasMorph) {
            $this->query->whereHasMorph(
                $this->whereHasMorph[0],
                $this->whereHasMorph[1],
                $this->whereHasMorph[2]
            );
        }

        foreach ($this->whereHas as $relation) {
            $this->query->whereHas($relation);
        }

        foreach ($this->with as $relation) {
            $this->query->with($relation);
        }

        return $this;
    }

    /**
     * Set clauses on the query builder.
     *
     * @return $this
     */
    protected function setClauses()
    {
        if (! is_null($this->select)) {
            $this->query->select($this->select);
        }

        if (isset($this->selectRaw) and ! is_null($this->selectRaw)) {
            $this->query->selectRaw($this->selectRaw);
        }

        foreach ($this->withCount as $relation) {
            $this->query->withCount($relation);
        }

        foreach ($this->wheres as $where) {
            if (isset($where['query'])) {
                $this->query->whereRaw($where['query'], $where['parameters']);
            } else {
                $this->query->where($where['column'], $where['operator'], $where['value']);
            }
        }

        foreach ($this->whereIns as $whereIn) {
            $this->query->whereIn($whereIn['column'], $whereIn['values']);
        }

        foreach ($this->whereBetween as $whereBetween) {
            $this->query->whereBetween($whereBetween['column'], $whereBetween['values']);
        }

        foreach ($this->orderBys as $orders) {
            $this->query->orderBy($orders['column'], $orders['direction']);
        }

        foreach ($this->when as $whens) {
            $this->query->when($whens['condition'] ?? false, $whens['callback']);
        }

        if (isset($this->skip) and ! is_null($this->skip)) {
            $this->query->skip($this->skip);
        }

        if (isset($this->take) and ! is_null($this->take)) {
            $this->query->take($this->take);
        }

        return $this;
    }

    /**
     * Set query scopes.
     *
     * @return $this
     */
    protected function setScopes()
    {
        foreach ($this->scopes as $method => $args) {
            $this->query->$method(implode(', ', $args));
        }

        return $this;
    }

    /**
     * Reset the query clause parameter arrays.
     *
     * @return $this
     */
    protected function unsetClauses()
    {
        $this->wheres = [];
        $this->whereIns = [];
        $this->when = [];
        $this->scopes = [];
        $this->take = null;
        $this->skip = null;
        $this->selectRaw = null;

        return $this;
    }
}
