<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;

class NoFiltersAllowed implements AllowedFilterList
{
    public function ensureAllowed(PendingFilter $pendingFilter): ApprovedFilter
    {
        throw new DeniedFilterException($pendingFilter);
    }
}
