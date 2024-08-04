<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits;

trait HasModifiers
{
    public function hasModifier(string $modifier): bool
    {
        return collect($this->modifiers)->contains($modifier);
    }
}
