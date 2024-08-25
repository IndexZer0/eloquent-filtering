<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext;

use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\CustomFilterParser as CustomFilterParserContract;
use IndexZer0\EloquentFiltering\Filter\FilterParsers\CustomFilterParser;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasEloquentContext;

trait CustomFilter
{
    use HasEloquentContext;

    public static function context(): FilterContext
    {
        return FilterContext::CUSTOM;
    }

    public static function format(): array
    {
        return [];
    }

    public static function customFilterParser(): CustomFilterParserContract
    {
        return new CustomFilterParser();
    }
}
