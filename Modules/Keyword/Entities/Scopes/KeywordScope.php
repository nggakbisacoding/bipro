<?php

namespace Modules\Keyword\Entities\Scopes;

trait KeywordScope
{
    /**
     * Scope a query to only include results that match the given search term.
     *
     * @param  mixed  $query The query to be scoped.
     * @param  string  $term The search term to be matched.
     * @return mixed The modified query.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->whereRaw('LOWER(name) LIKE ? ', ['%'.$term.'%']);
        });
    }
}
