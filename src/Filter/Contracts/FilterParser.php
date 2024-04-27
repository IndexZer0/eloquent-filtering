<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use IndexZer0\EloquentFiltering\Filter\FilterCollection;

interface FilterParser
{
    public function parse(array $filters): FilterCollection;
}
