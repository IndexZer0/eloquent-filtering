<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use IndexZer0\EloquentFiltering\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

interface AllowedFilter
{
    public function allowedFilters(): AllowedFilterList;

    public function getAllowedType(PendingFilter $pendingFilter): ?AllowedType;

    public function getTarget(PendingFilter $pendingFilter): ?Target;
}
