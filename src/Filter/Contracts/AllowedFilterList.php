<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

interface AllowedFilterList
{
    public function ensureAllowed(PendingFilter $pendingFilter): ApprovedFilter;
}
