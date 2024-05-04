<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Suppression\Suppression;
use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;

class SortApplier
{
    public function __construct(protected AllowedSortList $allowedSortList)
    {
    }

    public function apply(Builder $query, array $sorts): Builder
    {
        foreach ($sorts as $sort) {
            Suppression::honour(
                fn () => $this->applySort($query, $sort),
            );
        }

        return $query;
    }

    private function applySort(Builder $query, array $sort): Builder
    {
        $this->allowedSortList->ensureAllowed($sort['target']);

        return $query->orderBy($sort['target'], $sort['value']);
    }
}
