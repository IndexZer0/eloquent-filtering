<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering;

use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterSet;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MissingFilterSetException;

class FilterSetAllowedFilterResolver
{
    public function __construct(
        protected FilterSet|string $filterSet,
        protected IsFilterable $model
    ) {

    }

    public function resolve(): AllowedFilterList
    {
        $filterSet = $this->resolveFilterSet($this->filterSet);

        return $this->resolveFilterSetExtends($filterSet);
    }

    private function resolveFilterSet(FilterSet|string $filterSet): FilterSet
    {
        if ($filterSet instanceof FilterSet) {
            return $filterSet;
        }

        $filterSets = $this->model->getFilterSets();

        $filterSetObj = $filterSets->find($filterSet);

        if ($filterSetObj === null) {
            MissingFilterSetException::throw($filterSet);
        }

        return $filterSetObj;
    }

    private function resolveFilterSetExtends(FilterSet $filterSet): AllowedFilterList
    {
        $allowedFilters = $filterSet->allowedFilters();

        foreach ($filterSet->getExtends() as $extend) {
            $allowedFilters = $allowedFilters->add(
                ...$this->resolveFilterSetExtends(
                    $this->resolveFilterSet($extend)
                )->getAllowedFilters()
            );
        }

        return $allowedFilters;
    }
}
