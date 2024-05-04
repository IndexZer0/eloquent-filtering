<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;
use IndexZer0\EloquentFiltering\Sort\Exceptions\DeniedSortException;

class SomeSortsAllowed implements AllowedSortList
{
    protected Collection $allowedSorts;

    public function __construct(SortableField ...$sortableFields)
    {
        $this->allowedSorts = collect($sortableFields);
    }

    public function ensureAllowed(string $field): bool
    {
        foreach ($this->allowedSorts as $allowedSort) {
            if ($allowedSort->target()->isFor($field)) {
                return true;
            }
        }

        throw new DeniedSortException($field);
    }
}
