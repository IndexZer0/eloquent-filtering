<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface AppliesToTarget
{
    public static function targetKey(): string;
}
