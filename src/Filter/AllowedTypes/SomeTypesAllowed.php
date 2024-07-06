<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedTypes;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\RequestedFilter;

class SomeTypesAllowed implements AllowedTypes
{
    protected array $allowedTypes = [];

    public function __construct(
        AllowedType ...$allowedTypes,
    ) {
        $this->allowedTypes = $allowedTypes;
    }

    public function contains(RequestedFilter $requestedFilter): bool
    {
        foreach ($this->allowedTypes as $allowedType) {
            if ($allowedType->matches($requestedFilter)) {
                return true;
            }
        }
        return false;
    }
}
