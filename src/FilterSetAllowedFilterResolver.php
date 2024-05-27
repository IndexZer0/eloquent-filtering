<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering;

use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterSet;
use IndexZer0\EloquentFiltering\Filter\FilterSets\FilterSets;

class FilterSetAllowedFilterResolver
{
    protected FilterSets $modelFilterSets;

    public function __construct(
        protected FilterSet|string $filterSet,
        protected IsFilterable $model
    ) {
        $this->modelFilterSets = $this->model->getFilterSets();
    }

    public function resolve(): AllowedFilterList
    {
        $filterSet = $this->filterSet instanceof FilterSet ?: $this->getFilterSet($this->filterSet);

        return $this->resolveToAllowedFilterList($filterSet);
    }

    private function getFilterSet(FilterSet|string $filterSet): FilterSet
    {
        if ($filterSet instanceof FilterSet) {
            return $filterSet;
        }

        return $this->modelFilterSets->find($filterSet);
    }

    private function resolveToAllowedFilterList(FilterSet $filterSet): AllowedFilterList
    {
        $allowedFilters = $filterSet->allowedFilters();

        foreach ($filterSet->getExtends() as $extend) {
            $allowedFilters = $allowedFilters->add(
                ...$this->resolveToAllowedFilterList($this->getFilterSet($extend))
                    ->getAllowedFilters()
            );
        }

        return $allowedFilters;
    }
}
