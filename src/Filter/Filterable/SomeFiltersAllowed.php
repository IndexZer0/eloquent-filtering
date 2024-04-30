<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;

class SomeFiltersAllowed implements FilterableList
{
    protected Collection $allowedFilters;

    public function __construct(AllowedFilter ...$allowedFilters)
    {
        $this->allowedFilters = collect($allowedFilters);
    }

    public function ensureAllowed(PendingFilter $pendingFilter): PendingFilter
    {
        if ($pendingFilter->usage() === FilterMethod::USAGE_CONDITION) {
            // These are filters such as '$or'.
            return $pendingFilter->withFilterableList($this);
        }

        foreach ($this->allowedFilters as $allowedFilter) {
            if ($allowedFilter->matches($pendingFilter)) {
                $pendingFilter = $allowedFilter->hydrate($pendingFilter);
                return $pendingFilter->withFilterableList($allowedFilter->allowedFilters());
            }
        }

        throw new DeniedFilterException($pendingFilter);
    }
}
