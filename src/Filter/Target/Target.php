<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Target;

class Target
{
    public static function alias(string $from, string $to): Alias
    {
        return new Alias($from, $to);
    }
}
