<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;
use IndexZer0\EloquentFiltering\Sort\Exceptions\DeniedSortException;

class SomeSortsAllowed implements AllowedSortList
{
    protected Collection $allowedSorts;

    public function __construct(SortableField ...$sortableFields)
    {
        $this->allowedSorts = collect($sortableFields);
    }

    public function ensureAllowed(PendingSort $pendingSort): ApprovedSort
    {
        foreach ($this->allowedSorts as $allowedSort) {
            if ($allowedSort->target()->isFor($pendingSort->target())) {
                return $pendingSort->approveWith($allowedSort->target());
            }
        }

        throw new DeniedSortException($pendingSort);
    }
}
