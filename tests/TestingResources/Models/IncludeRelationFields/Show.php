<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Filterable\SomeFiltersAllowed;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;
use IndexZer0\EloquentFiltering\Target\Target;

class Show extends Model
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function allowedFilters(): SomeFiltersAllowed
    {
        return Filter::only(
            Filter::field('name', ['$eq']),
            Filter::field('description', ['$eq']),
            Filter::field(Target::alias('organizer', 'organizer_name'), ['$eq']),
            Filter::relation(Target::alias('e', 'events'), ['$has'])->includeRelationFields()
                ->andNestedRelation(
                    Filter::relation('tickets', ['$has'])->includeRelationFields()
                ),
        );
    }
}
