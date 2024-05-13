<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterSets;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterSet as FilterSetContract;
use IndexZer0\EloquentFiltering\Filter\Exceptions\InvalidArgumentException;

class ClassFilterSetInstantiator
{
    public function __invoke(string|FilterSetContract $filterSet): FilterSetContract
    {
        if (!is_string($filterSet)) {
            return $filterSet;
        }

        if (!is_a($filterSet, FilterSetContract::class, true)) {
            throw new InvalidArgumentException('Class must be a FilterSet');
        }

        return new $filterSet();
    }
}
