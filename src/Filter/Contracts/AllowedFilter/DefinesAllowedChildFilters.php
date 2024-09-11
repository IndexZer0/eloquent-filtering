<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;

interface DefinesAllowedChildFilters
{
    public function allowedFilters(): AllowedFilterList;
}
