<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\RequestedFilter;

interface AllowedTypes
{
    public function get(RequestedFilter $requestedFilter): ?AllowedType;
}
