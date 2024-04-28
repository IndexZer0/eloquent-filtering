<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;

class UnrestrictedFilterableList implements FilterableList
{
    public function ensureAllowed(string $type, ?string $target): UnrestrictedFilterableList
    {
        return new self();
    }
}
