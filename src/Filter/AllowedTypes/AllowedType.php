<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedTypes;

use IndexZer0\EloquentFiltering\Filter\AllowedModifiers\AllModifiersAllowed;
use IndexZer0\EloquentFiltering\Filter\AllowedModifiers\SomeModifiersAllowed;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedModifiers;
use IndexZer0\EloquentFiltering\Filter\RequestedFilter;

class AllowedType
{
    public function __construct(
        public string $type,
        public AllowedModifiers $allowedModifiers = new AllModifiersAllowed(),
    ) {
    }

    public function withModifiers(string ...$modifiers): AllowedType
    {
        $this->allowedModifiers = new SomeModifiersAllowed(...$modifiers);
        return $this;
    }

    public function matches(RequestedFilter $requestedFilter): bool
    {
        if ($this->type === $requestedFilter->type
            && $this->allowedModifiers->containsAll(...$requestedFilter->modifiers)
        ) {
            return true;
        }

        return false;
    }
}
