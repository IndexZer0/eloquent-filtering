<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableDefinition;

class FilterableRelation implements FilterableDefinition
{
    public function __construct(
        protected string $target,
        protected array  $types,
        protected array  $filterableDefinitions,
    ) {

    }

    public function target(): string
    {
        return $this->target;
    }

    public function types(): array
    {
        return $this->types;
    }

    public function definitions(): array
    {
        return $this->filterableDefinitions;
    }
}
