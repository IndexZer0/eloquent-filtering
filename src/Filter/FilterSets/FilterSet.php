<?php

namespace IndexZer0\EloquentFiltering\Filter\FilterSets;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterSet as FilterSetContract;

class FilterSet implements FilterSetContract
{
    protected array $extends = [];

    public function __construct(
        protected string $name,
        protected AllowedFilterList $allowedFilters
    ) { }

    public function name(): string
    {
        return $this->name;
    }

    public function allowedFilters(): AllowedFilterList
    {
        return $this->allowedFilters;
    }

    public function extends(string|array $extends): self
    {
        $this->extends = array_merge($this->extends, is_array($extends) ? $extends : [$extends]);
        return $this;
    }

    public function getExtends(): array
    {
        return $this->extends;
    }
}
