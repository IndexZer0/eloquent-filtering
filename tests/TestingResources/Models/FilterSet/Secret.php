<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\FilterSet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterSets\FilterSets;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;

class Secret extends Model implements IsFilterable
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    /*
     * ----------------------------------
     * IsFilterable interface methods
     * ----------------------------------
     */

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('name', ['$eq'])
        );
    }

    public function filterSets(): FilterSets
    {
        return Filter::sets(
            Filter::set('two', Filter::only(
                Filter::field('value', ['$like'])
            ))->extends('default')
        );
    }


    /*
     * ----------------------------------
     * Relations
     * ----------------------------------
     */

    public function environment(): BelongsTo
    {
        return $this->belongsTo(Environment::class);
    }
}
