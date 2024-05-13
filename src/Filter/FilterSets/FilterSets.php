<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterSets;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterSet as FilterSetContract;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MissingFilterSetException;

class FilterSets
{
    protected Collection $filterSets;

    public function __construct(FilterSetContract ...$filterSets)
    {
        $this->filterSets = collect($filterSets)
            ->keyBy(
                fn (FilterSetContract $filterSet) => $filterSet->name()
            );
    }

    public function find(string $name): FilterSetContract
    {
        if (!$this->filterSets->has($name)) {
            MissingFilterSetException::throw($name);
        }

        return $this->filterSets->get($name);
    }

    public function add(FilterSetContract $filterSet): self
    {
        $this->filterSets->put('default', $filterSet);
        return $this;
    }
}
