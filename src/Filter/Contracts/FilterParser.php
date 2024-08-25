<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;

interface FilterParser
{
    public function parse(
        Model $model,
        array $filters,
        AllowedFilterList $allowedFilterList,
        ?Relation $relation = null,
    ): FilterCollection;
}
