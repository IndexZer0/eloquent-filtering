<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterSets;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterSet as FilterSetContract;

abstract class ClassFilterSet implements FilterSetContract
{
    protected array $extends = [];

    public function name(): string
    {
        return static::class;
    }

    public function extends(string|array $extends): FilterSetContract
    {
        $this->extends = array_merge($this->extends, is_array($extends) ? $extends : [$extends]);
        return $this;
    }

    public function getExtends(): array
    {
        return $this->extends;
    }
}
