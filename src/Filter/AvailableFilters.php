<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MissingFilterException;

class AvailableFilters
{
    public Collection $filters;

    public function __construct()
    {
        /** @var AvailableFiltersLoader $loader */
        $loader = resolve(AvailableFiltersLoader::class);
        $this->filters = $loader();
    }

    public function find(string $type): string
    {
        if (!$this->filters->has($type)) {
            MissingFilterException::throw($type);
        }

        return $this->filters->get($type);
    }
}
