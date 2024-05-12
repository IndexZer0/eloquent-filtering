<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;

class AllowedFilterResolver
{
    public function __construct(
        protected AllowedFilterList $allowedFilterList,
        protected string $modelFqcn
    ) {

    }

    public function resolve(): AllowedFilterList
    {
        return $this->allowedFilterList->resolveRelationsAllowedFilters($this->modelFqcn);
    }
}
