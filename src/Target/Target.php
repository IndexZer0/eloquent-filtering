<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Target;

use IndexZer0\EloquentFiltering\Contracts\Target as TargetContract;

class Target
{
    public static function alias(string $target, ?string $targetAlias = null): AliasedTarget
    {
        return new AliasedTarget($target, $targetAlias);
    }

    public static function relationAlias(string $target, ?string $targetAlias = null, TargetContract ...$targets): AliasedRelationTarget
    {
        return new AliasedRelationTarget(
            $target,
            $targetAlias,
            ...$targets,
        );
    }
}
