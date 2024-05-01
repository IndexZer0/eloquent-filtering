<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Target;

readonly class Alias
{
    public function __construct(
        public string  $target,
        public ?string $targetAlias = null
    ) {
    }

    public function isFor(string $target): bool
    {
        return $this->target === $target;
    }
}
