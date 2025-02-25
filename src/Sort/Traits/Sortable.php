<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Traits;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Sort\Contracts\AllowedSortList;
use IndexZer0\EloquentFiltering\Sort\Contracts\SortValidator;
use IndexZer0\EloquentFiltering\Sort\Sortable\Sort;
use IndexZer0\EloquentFiltering\Sort\SortApplier;

// @phpstan-ignore trait.unused
trait Sortable
{
    public function scopeSort(
        Builder          $query,
        array            $pendingSorts,
        ?AllowedSortList $allowedSortList = null,
    ): Builder {

        /** @var SortValidator $sortValidator */
        $sortValidator = resolve(SortValidator::class);
        $pendingSorts = $sortValidator->validate($pendingSorts);

        $sortApplier = new SortApplier($allowedSortList ?? $this->allowedSorts());
        return $sortApplier->apply($query, $pendingSorts);

    }

    protected function allowedSorts(): AllowedSortList
    {
        $defaultAllowedList = config('eloquent-filtering.default_allowed_sort_list', 'none');

        return $defaultAllowedList === 'none' ? Sort::none() : Sort::all();
    }
}
