<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedTypes;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\RequestedFilter;

class AllTypesAllowed implements AllowedTypes
{
    public function get(RequestedFilter $requestedFilter): ?AllowedType
    {
        $allowedType = new AllowedType($requestedFilter->type);

        if ($allowedType->matches($requestedFilter)) {
            return $allowedType;
        }

        return null;
    }
}
