<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\MorphToMany;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;

class Task extends Model implements IsFilterable
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    public function individuals(): MorphToMany
    {
        return $this->morphedByMany(Individual::class, 'taskable')->using(Taskable::class);
    }

    public function groups(): MorphToMany
    {
        return $this->morphedByMany(Group::class, 'taskable')->using(Taskable::class);
    }

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('name', [FilterType::EQUAL]),
            Filter::relation('individuals', [FilterType::HAS])->includeRelationFields(),
            Filter::relation('groups', [FilterType::HAS])->includeRelationFields(),
        );
    }
}
