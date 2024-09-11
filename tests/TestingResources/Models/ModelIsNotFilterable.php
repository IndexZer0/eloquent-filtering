<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models;

use Illuminate\Database\Eloquent\Model;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;

class ModelIsNotFilterable extends Model
{
    use Filterable;

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('name', [FilterType::EQUAL]),
        );
    }
}
