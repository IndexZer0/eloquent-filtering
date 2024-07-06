<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedTypes;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\RequestedFilter;

class AllTypesAllowed implements AllowedTypes
{
    public function contains(RequestedFilter $requestedFilter): bool
    {
        return true;
    }
}
