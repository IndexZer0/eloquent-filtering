<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext;

use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\CustomFilterParser;
use IndexZer0\EloquentFiltering\Filter\FilterParsers\FieldFilterParser;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasEloquentContext;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasTarget;

trait FieldFilter
{
    use HasEloquentContext;
    use HasTarget;

    public static function context(): FilterContext
    {
        return FilterContext::FIELD;
    }

    public static function customFilterParser(): CustomFilterParser
    {
        return new FieldFilterParser();
    }
}
