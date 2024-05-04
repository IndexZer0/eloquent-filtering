<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;

class NoSortsAllowed implements AllowedSortList
{
    public function ensureAllowed(string $field): bool
    {
        return false;
    }
}
