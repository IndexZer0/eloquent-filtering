<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Filterable\AllFiltersAllowed;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Traits\CanBeRequired;

class AllowedField implements AllowedFilter
{
    use CanBeRequired;

    public function __construct(
        protected Target $target,
        protected AllowedTypes $types,
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

    public function getAllowedType(PendingFilter $pendingFilter): ?AllowedType
    {
        if (!$pendingFilter->is(FilterContext::FIELD)) {
            return null;
        }

        if (!$this->target->isFor($pendingFilter->desiredTarget())) {
            return null;
        }

        return $this->types->get($pendingFilter->requestedFilter());
    }

    public function getTarget(PendingFilter $pendingFilter): Target
    {
        return $this->target->getForApprovedFilter($pendingFilter);
    }

    public function getDescription(): string
    {
        return sprintf('"%s" filter', $this->target->target());
    }
}
