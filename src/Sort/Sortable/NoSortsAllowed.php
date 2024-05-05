<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;
use IndexZer0\EloquentFiltering\Sort\Exceptions\DeniedSortException;

class NoSortsAllowed implements AllowedSortList
{
    public function ensureAllowed(PendingSort $pendingSort): ApprovedSort
    {
        throw new DeniedSortException($pendingSort);
    }
}
