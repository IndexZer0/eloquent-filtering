<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Sort\Contracts\SortableList;
use IndexZer0\EloquentFiltering\Sort\Exceptions\DeniedSortException;

class RestrictedSortableList implements SortableList
{
    protected Collection $list;

    public function __construct(SortableColumn ...$sortableColumns)
    {
        $this->list = collect($sortableColumns)->keyBy(
            fn (SortableColumn $sortableColumn) => $sortableColumn->target()
        );
    }

    public function ensureAllowed(string $column): bool
    {
        if (!$this->list->has($column)) {
            DeniedSortException::throw($column);
        }

        return true;
    }
}
