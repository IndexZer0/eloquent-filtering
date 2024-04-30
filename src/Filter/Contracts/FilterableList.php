<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

interface FilterableList
{
    public function ensureAllowed(PendingFilter $pendingFilter): PendingFilter;
}
