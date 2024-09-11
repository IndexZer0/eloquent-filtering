<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedTypes;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\RequestedFilter;

class SomeTypesAllowed implements AllowedTypes
{
    protected Collection $allowedTypes;

    public function __construct(
        AllowedType ...$allowedTypes,
    ) {
        $this->allowedTypes = collect($allowedTypes)->keyBy('type');
    }

    public function get(RequestedFilter $requestedFilter): ?AllowedType
    {
        $allowedType = $this->allowedTypes->get($requestedFilter->type);

        if ($allowedType !== null && $allowedType->matches($requestedFilter)) {
            return $allowedType;
        }

        return null;
    }
}
