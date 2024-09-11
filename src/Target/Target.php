<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Target;

class Target
{
    public static function alias(string $target, ?string $targetAlias = null): AliasedTarget
    {
        return new AliasedTarget($target, $targetAlias);
    }
}
