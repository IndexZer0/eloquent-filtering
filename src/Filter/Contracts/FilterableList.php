<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface FilterableList
{
    public function ensureAllowed(FilterMethod $filter): FilterableList;
}
