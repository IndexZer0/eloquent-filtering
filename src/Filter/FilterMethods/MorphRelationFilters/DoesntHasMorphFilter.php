<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\MorphRelationFilters;

use IndexZer0\EloquentFiltering\Filter\FilterType;

class DoesntHasMorphFilter extends HasMorphFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return FilterType::DOESNT_HAS_MORPH->value;
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function operator(): string
    {
        return '<';
    }
}
