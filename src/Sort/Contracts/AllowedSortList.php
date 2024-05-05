<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Contracts;

use IndexZer0\EloquentFiltering\Sort\Sortable\ApprovedSort;
use IndexZer0\EloquentFiltering\Sort\Sortable\PendingSort;

interface AllowedSortList
{
    public function ensureAllowed(PendingSort $pendingSort): ApprovedSort;
}
