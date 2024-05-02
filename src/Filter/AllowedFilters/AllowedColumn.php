<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Filterable\AllFiltersAllowed;
use IndexZer0\EloquentFiltering\Filter\Target\Alias;
use IndexZer0\EloquentFiltering\Filter\Traits\HydratesAlias;

class AllowedColumn implements AllowedFilter
{
    use HydratesAlias;

    public function __construct(
        protected Alias $target,
        protected AllowedTypes $types,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public function allowedFilters(): FilterableList
    {
        return new AllFiltersAllowed();
    }

    public function matches(PendingFilter $pendingFilter): bool
    {
        if ($pendingFilter->usage() !== FilterMethod::USAGE_COLUMN) {
            return false;
        }

        return $this->types->contains($pendingFilter->type()) &&
            $this->target->isFor($pendingFilter->target());
    }

    public function hydrate(PendingFilter $pendingFilter): PendingFilter
    {
        return $this->hydrateAlias($pendingFilter, $this->target);
    }
}
