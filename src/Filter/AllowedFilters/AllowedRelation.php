<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

class AllowedRelation implements AllowedFilter
{
    public function __construct(
        protected Target            $target,
        protected AllowedTypes      $types,
        protected AllowedFilterList $allowedFilters,
        protected ?string           $alias = null,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public function allowedFilters(): AllowedFilterList
    {
        return $this->allowedFilters;
    }

    public function matches(PendingFilter $pendingFilter): bool
    {
        if (!$pendingFilter->is(FilterMethod::USAGE_RELATION)) {
            return false;
        }

        return $this->types->contains($pendingFilter->type()) &&
            $this->target->isFor($pendingFilter->desiredTarget());
    }

    public function getTarget(PendingFilter $pendingFilter): ?Target
    {
        return $this->target;
    }
}
