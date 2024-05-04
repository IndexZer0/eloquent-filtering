<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Filterable\AllFiltersAllowed;

class AllowedColumn implements AllowedFilter
{
    public function __construct(
        protected Target $target,
        protected AllowedTypes  $types,
    ) {
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
        if (!$pendingFilter->is(FilterMethod::USAGE_COLUMN)) {
            return false;
        }

        return $this->types->contains($pendingFilter->type()) &&
            $this->target->isFor($pendingFilter->desiredTarget());
    }

    public function getTarget(PendingFilter $pendingFilter): Target
    {
        return $this->target;
    }
}
