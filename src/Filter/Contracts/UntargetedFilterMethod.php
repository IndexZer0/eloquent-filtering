<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface UntargetedFilterMethod extends FilterMethod
{
    public function hasTarget(): false;
}
