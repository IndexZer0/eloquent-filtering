<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Traits;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Sort\Contracts\SortableList;
use IndexZer0\EloquentFiltering\Sort\Sortable\UnrestrictedSortableList;
use IndexZer0\EloquentFiltering\Sort\SortApplier;

trait Sortable
{
    public function scopeSort(
        Builder $query,
        array $sorts,
        SortableList $sortableList = new UnrestrictedSortableList()
    ): Builder {

        $sortApplier = new SortApplier($sortableList);
        return $sortApplier->apply($query, $sorts);

    }
}
