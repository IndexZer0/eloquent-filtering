<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Traits\EnsuresChildFiltersAllowed;
use IndexZer0\EloquentFiltering\Target\Target;

class AllFiltersAllowed implements AllowedFilterList
{
    use EnsuresChildFiltersAllowed;

    public function ensureAllowed(PendingFilter $pendingFilter): ApprovedFilter
    {
        // TODO allow developer to specify alias when allowing all.

        $childFilters = $this->ensureChildFiltersAllowed($pendingFilter, $this);

        return $pendingFilter->approveWith(
            $pendingFilter->is(FilterMethod::USAGE_CONDITION) ? null : Target::alias($pendingFilter->desiredTarget()),
            $childFilters
        );
    }
}
