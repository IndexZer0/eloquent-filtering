<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterSet;
use IndexZer0\EloquentFiltering\FilterSetAllowedFilterResolver;

class AllowedFilterResolver
{
    public function __construct(
        protected FilterSet|string|AllowedFilterList|null $allowedFilters,
        protected IsFilterable $model
    ) {

    }

    public function resolve(): AllowedFilterList
    {
        $allowedFilterList = $this->resolveToAllowedFilterList();

        return $allowedFilterList->resolveRelationsAllowedFilters($this->model::class);
    }

    private function resolveToAllowedFilterList(): AllowedFilterList
    {
        if ($this->allowedFilters instanceof AllowedFilterList) {
            return $this->allowedFilters;
        }

        if ($this->allowedFilters === null) {
            return $this->model->allowedFilters();
        }

        return (new FilterSetAllowedFilterResolver($this->allowedFilters, $this->model))->resolve();
    }
}
