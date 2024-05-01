<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Target;

class Target
{
    public static function alias(string $target, string $targetAlias): Alias
    {
        return new Alias($target, $targetAlias);
    }
}
