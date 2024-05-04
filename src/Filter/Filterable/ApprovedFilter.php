<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\AppliesToTarget;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;

class ApprovedFilter
{
    public function __construct(
        protected string $filterFqcn,
        protected array  $data,
        protected ?Target           $target = null,
        protected ?FilterCollection $childFilters = null,
    ) {
    }

    private function appliesToTarget(string $filterFqcn): bool
    {
        return is_a($filterFqcn, AppliesToTarget::class, true);
    }

    public function createFilter(): FilterMethod
    {
        $filterFqcn = $this->filterFqcn;

        if ($this->appliesToTarget($filterFqcn)) {
            /** @var $filterFqcn AppliesToTarget */
            unset($this->data[$filterFqcn::targetKey()]);
        }

        return $filterFqcn::from($this);
    }

    public function target(): ?Target
    {
        return $this->target;
    }

    public function childFilters(): ?FilterCollection
    {
        return $this->childFilters;
    }

    public function data_get(string $key): mixed
    {
        return data_get($this->data, $key);
    }
}
