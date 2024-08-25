<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Filterable\SomeFiltersAllowed;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;
use IndexZer0\EloquentFiltering\Target\Target;

class Event extends Model implements IsFilterable
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
            Filter::field('starting_at', [FilterType::BETWEEN]),
            Filter::field('finishing_at', [FilterType::EQUAL]),
            Filter::field(Target::alias('audience', 'audience_limit'), [FilterType::EQUAL]),
            Filter::relation('show', [FilterType::HAS])->includeRelationFields(),
            Filter::relation('tickets', [FilterType::HAS])->includeRelationFields(),
        );
    }

    /*
     * ----------------------------------
     * Relations
     * ----------------------------------
     */

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
