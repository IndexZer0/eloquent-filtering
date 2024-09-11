<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext;

use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\CustomFilterParser;
use IndexZer0\EloquentFiltering\Filter\FilterParsers\MorphRelationFilterParser;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasEloquentContext;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasMorphTypes;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasTarget;

trait MorphRelationFilter
{
    use HasEloquentContext;
    use HasTarget;
    use HasMorphTypes;

    public static function context(): FilterContext
    {
        return FilterContext::MORPH_RELATION;
    }

    public static function format(): array
    {
        return [];
    }

    public static function customFilterParser(): CustomFilterParser
    {
        return new MorphRelationFilterParser();
    }
}
