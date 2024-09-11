<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;
use IndexZer0\EloquentFiltering\Target\Target;

class Show extends Model implements IsFilterable
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
            Filter::field('name', [FilterType::EQUAL]),
            Filter::field('description', [FilterType::EQUAL]),
            Filter::field(Target::alias('organizer', 'organizer_name'), [FilterType::EQUAL]),
            Filter::relation(Target::alias('e', 'events'), [FilterType::HAS])->includeRelationFields()
                ->andNestedRelation(
                    Filter::relation('tickets', [FilterType::HAS])->includeRelationFields()
                ),
        );
    }

    /*
     * ----------------------------------
     * Relations
     * ----------------------------------
     */

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
