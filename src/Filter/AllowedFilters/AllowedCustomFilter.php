<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Filterable\AllFiltersAllowed;

class AllowedCustomFilter implements AllowedFilter
{
    public function __construct(protected array $types)
    {
    }

    public function allowedFilters(): FilterableList
    {
        return new AllFiltersAllowed();
    }

    public function matches(PendingFilter $pendingFilter): bool
    {
        if ($pendingFilter->usage() !== FilterMethod::USAGE_CUSTOM) {
            return false;
        }

        return in_array($pendingFilter->type(), $this->types, true);
    }

    public function hydrate(PendingFilter $pendingFilter): PendingFilter
    {
        return $pendingFilter;
    }
}
