<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext;

use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\CustomFilterParser;
use IndexZer0\EloquentFiltering\Filter\FilterParsers\ConditionalFilterParser;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasChildFilters;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasEloquentContext;

trait ConditionFilter
{
    use HasEloquentContext;
    use HasChildFilters;

    public static function context(): FilterContext
    {
        return FilterContext::CONDITION;
    }

    public static function customFilterParser(): CustomFilterParser
    {
        return new ConditionalFilterParser();
    }
}
