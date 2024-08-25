<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;

use IndexZer0\EloquentFiltering\Filter\FilterCollection;

interface HasChildFilters
{
    public function setChildFilters(FilterCollection $filters): void;
}
