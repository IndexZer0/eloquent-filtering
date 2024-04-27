<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface TargetedFilterMethod extends FilterMethod
{
    public function target(): string;

    public function hasTarget(): true;
}
