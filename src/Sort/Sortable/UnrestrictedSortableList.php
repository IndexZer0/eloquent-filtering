<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use IndexZer0\EloquentFiltering\Sort\Contracts\SortableList;

class UnrestrictedSortableList implements SortableList
{
    public function ensureAllowed(string $column): bool
    {
        return true;
    }
}
