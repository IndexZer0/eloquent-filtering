<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;

interface Modifiable
{
    public static function supportedModifiers(): array;

    public function setModifiers(array $modifiers): void;

    public function hasModifier(string $modifier): bool;
}
