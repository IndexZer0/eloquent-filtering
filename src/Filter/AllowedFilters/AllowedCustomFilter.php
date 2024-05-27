<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Filterable\AllFiltersAllowed;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

class AllowedCustomFilter implements AllowedFilter
{
    public function __construct(protected AllowedTypes $types)
    {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public function allowedFilters(): AllowedFilterList
    {
        return new AllFiltersAllowed();
    }

    public function matches(PendingFilter $pendingFilter): bool
    {
        if (!$pendingFilter->is(FilterContext::CUSTOM)) {
            return false;
        }

        return $this->types->contains($pendingFilter->type());
    }

    public function getTarget(PendingFilter $pendingFilter): ?Target
    {
        return null;
    }
}
