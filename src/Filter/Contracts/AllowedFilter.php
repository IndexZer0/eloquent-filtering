<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

interface AllowedFilter
{
    public function allowedFilters(): AllowedFilterList;

    public function matches(PendingFilter $pendingFilter): bool;

    public function getTarget(PendingFilter $pendingFilter): ?Target;
}
