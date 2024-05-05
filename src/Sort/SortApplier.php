<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Sort\Sortable\PendingSort;
use IndexZer0\EloquentFiltering\Suppression\Suppression;
use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;

class SortApplier
{
    public function __construct(protected AllowedSortList $allowedSortList)
    {
    }

    public function apply(Builder $query, PendingSortCollection $sorts): Builder
    {
        foreach ($sorts as $sort) {
            Suppression::honour(
                fn () => $this->applySort($query, $sort),
            );
        }

        return $query;
    }

    private function applySort(Builder $query, PendingSort $pendingSort): Builder
    {
        $approvedSort = $this->allowedSortList->ensureAllowed($pendingSort);

        return $query->orderBy($approvedSort->target()->getReal(), $approvedSort->direction());
    }
}
