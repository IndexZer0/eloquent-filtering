<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;

use IndexZer0\EloquentFiltering\Filter\Morph\MorphTypes;

interface HasMorphFilters
{
    public function setMorphTypes(MorphTypes $types): void;
}
