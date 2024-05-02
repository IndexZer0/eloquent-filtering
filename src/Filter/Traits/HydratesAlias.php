<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits;

use IndexZer0\EloquentFiltering\Filter\Target\Alias;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

trait HydratesAlias
{
    protected function hydrateAlias(PendingFilter $pendingFilter, Alias $alias): PendingFilter
    {
        $filterFqcn = $pendingFilter->filterFqcn();
        $data = $pendingFilter->data();

        if ($alias->hasAlias()) {
            $data[$filterFqcn::targetKey()] = $alias->targetAlias;
        }

        return $pendingFilter->withData($data);
    }
}
