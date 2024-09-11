<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables;

use IndexZer0\EloquentFiltering\Filter\FilterCollection;

trait HasChildFilters
{
    protected FilterCollection $filters;

    public function setChildFilters(FilterCollection $filters): void
    {
        $this->filters = $filters;
    }

    public static function hasChildFiltersRules(): array
    {
        return [
            'value'   => ['array'],
            'value.*' => ['array'],
        ];
    }
}
