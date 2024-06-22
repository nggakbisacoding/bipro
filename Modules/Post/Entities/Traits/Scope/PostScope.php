<?php

namespace Modules\Post\Entities\Traits\Scope;

/**
 * Class PostScope.
 */
trait PostScope
{
    /**
     * @return mixed
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('name', 'like', '%'.$term.'%')
                ->orWhere('username', 'like', '%'.$term.'%')
                ->orWhere('message', 'like', '%'.$term.'%');
        });
    }

    public function scopeSearchByHashtag($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->whereRaw('LOWER(hashtags) LIKE ? ', ['%'.strtolower($term).'%']);
        });
    }
}
