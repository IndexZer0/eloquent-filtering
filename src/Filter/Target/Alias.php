<?php

namespace IndexZer0\EloquentFiltering\Filter\Target;

readonly class Alias
{
    public function __construct(
        public string  $target,
        public ?string $targetAlias = null
    ) {}
}
