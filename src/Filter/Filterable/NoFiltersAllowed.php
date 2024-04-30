<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;

class NoFiltersAllowed implements FilterableList
{
    public function ensureAllowed(PendingFilter $pendingFilter): PendingFilter
    {
        throw new DeniedFilterException($pendingFilter);
    }
}
