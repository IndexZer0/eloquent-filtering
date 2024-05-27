<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface FilterSet
{
    public function name(): string;

    public function allowedFilters(): AllowedFilterList;

    public function extends(string|array $extends): FilterSet;

    public function getExtends(): array;
}
