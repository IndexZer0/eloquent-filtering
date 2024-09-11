<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;

interface Targetable
{
    public function setTarget(string $target): void;
}
