<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;

use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

interface AllowedFilter
{
    public function getAllowedType(PendingFilter $pendingFilter): ?AllowedType;

    public function getIdentifier(): string;
}
