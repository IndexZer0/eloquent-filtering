<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\AllFiltersAllowed;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Traits\CanBeRequired;

class AllowedCustomFilter implements AllowedFilter
{
    use CanBeRequired;

    public function __construct(protected AllowedType $type)
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

    public function getAllowedType(PendingFilter $pendingFilter): ?AllowedType
    {
        if (!$pendingFilter->is(FilterContext::CUSTOM)) {
            return null;
        }

        if ($this->type->matches($pendingFilter->requestedFilter())) {
            return $this->type;
        }

        return null;
    }

    public function getTarget(PendingFilter $pendingFilter): ?Target
    {
        return null;
    }

    public function getDescription(): string
    {
        return sprintf('"%s" filter', $this->type->type);
    }
}
