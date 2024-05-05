<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;
use IndexZer0\EloquentFiltering\Target\AliasedTarget;
use IndexZer0\EloquentFiltering\Target\Target;

class AllSortsAllowed implements AllowedSortList
{
    protected Collection $targets;

    public function __construct(AliasedTarget ...$targets)
    {
        $this->targets = collect($targets)->keyBy(
            fn (AliasedTarget $target) => $target->target
        );
    }

    public function ensureAllowed(PendingSort $pendingSort): ApprovedSort
    {
        $target = $this->targets->get($pendingSort->target());
        return $pendingSort->approveWith(
            $target ?? Target::alias($pendingSort->target()),
        );
    }
}
