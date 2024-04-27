<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableDefinition;

readonly class FilterableColumn implements FilterableDefinition
{
    public function __construct(public string $target, public array $types)
    {
    }

    public function target(): string
    {
        return $this->target;
    }

    public function types(): array
    {
        return $this->types;
    }
}
