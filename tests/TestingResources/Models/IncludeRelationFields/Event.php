<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;
use IndexZer0\EloquentFiltering\Target\Target;

class Event extends Model
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('starting_at', ['$between']),
            Filter::field('finishing_at', ['$eq']),
            Filter::field(Target::alias('audience', 'audience_limit'), ['$eq']),
            Filter::relation('show', ['$has'])->includeRelationFields(),
            Filter::relation('tickets', ['$has'])->includeRelationFields(),
        );
    }
}
