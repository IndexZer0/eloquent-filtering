<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;

readonly class UnrestrictedFilterableList implements FilterableList
{
    public function ensureAllowed(FilterMethod $filter): UnrestrictedFilterableList
    {
        return new self();
    }
}
