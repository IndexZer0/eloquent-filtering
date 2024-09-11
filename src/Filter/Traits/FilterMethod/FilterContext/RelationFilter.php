<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext;

use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\CustomFilterParser;
use IndexZer0\EloquentFiltering\Filter\FilterParsers\RelationFilterParser;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasChildFilters;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasEloquentContext;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasTarget;

trait RelationFilter
{
    use HasEloquentContext;
    use HasTarget;
    use HasChildFilters;

    public static function context(): FilterContext
    {
        return FilterContext::RELATION;
    }

    public static function customFilterParser(): CustomFilterParser
    {
        return new RelationFilterParser();
    }

    public static function format(): array
    {
        return [];
    }
}
