<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Morph;

use IndexZer0\EloquentFiltering\Filter\FilterCollection;

readonly class MorphType
{
    public function __construct(
        public string $type,
        public FilterCollection $filters,
    ) {
    }
}
