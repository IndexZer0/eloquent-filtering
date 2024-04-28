<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableDefinition;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;

class RestrictedFilterableList implements FilterableList
{
    protected Collection $list;

    public function __construct(FilterableDefinition ...$filterableDefinitions)
    {
        $this->list = collect($filterableDefinitions)->keyBy(
            fn (FilterableDefinition $filterableDefinition) => $filterableDefinition->target()
        );
    }

    public function ensureAllowed(string $type, ?string $target): RestrictedFilterableList
    {
        // TODO - how does this work for a custom filter that shouldn't always be allowed?
        if ($target === null) {
            // Always allow filters without a specific target to be allowed.
            // These are filters such as '$or'.
            return $this;
        }

        foreach ($this->list as $filterableDefinition) {
            if ($filterableDefinition->target() === $target && in_array($type, $filterableDefinition->types())) {
                return new self(...$filterableDefinition->definitions());
            }
        }

        throw new DeniedFilterException($type, $target);
    }
}
