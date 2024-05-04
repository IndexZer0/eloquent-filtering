<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Traits\EnsuresChildFiltersAllowed;
use IndexZer0\EloquentFiltering\Target\AliasedTarget;

class AllFiltersAllowed implements AllowedFilterList
{
    use EnsuresChildFiltersAllowed;

    public function ensureAllowed(PendingFilter $pendingFilter): ApprovedFilter
    {
        $childFilters = $this->ensureChildFiltersAllowed($pendingFilter, $this);

        return $pendingFilter->approveWith(
            $pendingFilter->is(FilterMethod::USAGE_CONDITION) ? null : new AliasedTarget($pendingFilter->desiredTarget()),
            $childFilters
        );
    }
}
