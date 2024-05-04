<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Traits\EnsuresChildFiltersAllowed;

class SomeFiltersAllowed implements AllowedFilterList
{
    use EnsuresChildFiltersAllowed;

    protected Collection $allowedFilters;

    public function __construct(AllowedFilter ...$allowedFilters)
    {
        $this->allowedFilters = collect($allowedFilters);
    }

    public function ensureAllowed(PendingFilter $pendingFilter): ApprovedFilter
    {
        if ($pendingFilter->is(FilterMethod::USAGE_CONDITION)) {
            // These are filters such as '$or'.
            return $pendingFilter->approveWith(
                childFilters: $this->ensureChildFiltersAllowed($pendingFilter, $this)
            );
        }

        foreach ($this->allowedFilters as $allowedFilter) {
            if ($allowedFilter->matches($pendingFilter)) {

                $allowedChildFilters = $allowedFilter->allowedFilters();

                $childFilters = $this->ensureChildFiltersAllowed($pendingFilter, $allowedChildFilters);

                return $pendingFilter->approveWith(
                    $allowedFilter->getTarget($pendingFilter),
                    $childFilters
                );
            }
        }

        throw new DeniedFilterException($pendingFilter);
    }
}
