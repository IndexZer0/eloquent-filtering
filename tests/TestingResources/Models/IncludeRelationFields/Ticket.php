<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Filterable\SomeFiltersAllowed;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;

class Ticket extends Model implements IsFilterable
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    /*
     * ----------------------------------
     * IsFilterable interface methods
     * ----------------------------------
     */

    public function allowedFilters(): SomeFiltersAllowed
    {
        return Filter::only(
            Filter::field('type', ['$eq']),
            Filter::field('price', ['$between']),
            Filter::relation('event', ['$has'])->includeRelationFields()
                ->andNestedRelation(Filter::relation('show', ['$has'])->includeRelationFields()),
        );
    }

    /*
     * ----------------------------------
     * Relations
     * ----------------------------------
     */

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
