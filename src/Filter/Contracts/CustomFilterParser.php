<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

interface CustomFilterParser
{
    public function parse(
        PendingFilter  $pendingFilter,
        ?AllowedFilter $allowedFilter = null,
        ?AllowedFilterList $allowedFilterList = null
    ): FilterMethod;
}
