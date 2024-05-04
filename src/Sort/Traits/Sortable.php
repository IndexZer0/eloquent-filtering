<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Traits;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;
use IndexZer0\EloquentFiltering\Sort\Sortable\Sort;
use IndexZer0\EloquentFiltering\Sort\SortApplier;

trait Sortable
{
    public function scopeSort(
        Builder $query,
        array $sorts,
        ?AllowedSortList $allowedSortList = null
    ): Builder {

        $sortApplier = new SortApplier($allowedSortList ?? $this->allowedSorts());
        return $sortApplier->apply($query, $sorts);

    }

    protected function allowedSorts(): AllowedSortList
    {
        $defaultAllowedList = config('eloquent-filtering.default_allowed_sort_list', 'none');

        return $defaultAllowedList === 'none' ? Sort::none() : Sort::all();
    }
}
