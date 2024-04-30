<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;

class AllFiltersAllowed implements FilterableList
{
    public function ensureAllowed(PendingFilter $pendingFilter): PendingFilter
    {
        return $pendingFilter->withFilterableList($this);
    }
}
