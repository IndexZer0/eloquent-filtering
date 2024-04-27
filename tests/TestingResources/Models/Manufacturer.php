<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models;

use Illuminate\Database\Eloquent\Model;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;

class Manufacturer extends Model
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    protected function allowedFilters(): FilterableList
    {
        return Filter::allow(
            Filter::column('name', ['$eq'])
        );
    }
}
