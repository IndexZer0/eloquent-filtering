<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;

class AllSortsAllowed implements AllowedSortList
{
    public function ensureAllowed(string $field): bool
    {
        return true;
    }
}
