<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Rules;

class TargetRules
{
    public static function get(): array
    {
        return ['target' => ['required', 'string']];
    }
}
