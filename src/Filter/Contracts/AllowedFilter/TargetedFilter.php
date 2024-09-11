<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;

use IndexZer0\EloquentFiltering\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

interface TargetedFilter
{
    public function getTarget(PendingFilter $pendingFilter): Target;
}
