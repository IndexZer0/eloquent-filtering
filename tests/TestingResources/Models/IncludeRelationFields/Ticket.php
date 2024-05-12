<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;

class Ticket extends Model
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('type', ['$eq']),
            Filter::field('price', ['$between']),
            Filter::relation('event', ['$has'])->includeRelationFields()
                ->andNestedRelation(Filter::relation('show', ['$has'])->includeRelationFields()),
        );
    }
}
