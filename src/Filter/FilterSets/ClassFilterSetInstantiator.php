<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterSets;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterSet as FilterSetContract;

class ClassFilterSetInstantiator
{
    public function __invoke(string|FilterSetContract $filterSet): FilterSetContract
    {
        if (!is_string($filterSet)) {
            return $filterSet;
        }

        if (!is_a($filterSet, FilterSetContract::class, true)) {
            throw new \Exception('TODO'); // TODO
        }

        return new $filterSet();
    }
}
