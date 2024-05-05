<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;
use IndexZer0\EloquentFiltering\Target\Target;

class AllSortsAllowed implements AllowedSortList
{
    public function ensureAllowed(PendingSort $pendingSort): ApprovedSort
    {
        return $pendingSort->approveWith(
            Target::alias($pendingSort->target()),
        );
    }
}
