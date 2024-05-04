<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;
use IndexZer0\EloquentFiltering\Sort\Exceptions\DeniedSortException;

class SomeSortsAllowed implements AllowedSortList
{
    protected Collection $list;

    public function __construct(SortableField ...$sortableFields)
    {
        $this->list = collect($sortableFields)->keyBy(
            fn (SortableField $sortableFields) => $sortableFields->target()
        );
    }

    public function ensureAllowed(string $field): bool
    {
        if (!$this->list->has($field)) {
            DeniedSortException::throw($field);
        }

        return true;
    }
}
