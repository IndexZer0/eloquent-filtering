<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\BelongsToMany\AllowedFilters;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\BelongsToMany\User;

class OnlyPivotFromUser
{
    public function __invoke(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('assigned_by', [FilterType::EQUAL])
                ->pivot(User::class),
        );
    }
}
