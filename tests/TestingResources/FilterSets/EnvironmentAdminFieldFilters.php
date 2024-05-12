<?php

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\FilterSets;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterSets\ClassFilterSet;

class EnvironmentAdminFieldFilters extends ClassFilterSet
{
    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('iam_user', ['$eq'])
        );
    }
}
