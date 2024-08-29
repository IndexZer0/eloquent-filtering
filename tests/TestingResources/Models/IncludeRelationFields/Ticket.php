<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
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

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('type', [FilterType::EQUAL]),
            Filter::field('price', [FilterType::BETWEEN]),
            Filter::relation('event', [FilterType::HAS])->includeRelationFields()
                ->andNestedRelation(
                    Filter::relation('show', [FilterType::HAS])->includeRelationFields()
                ),
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
