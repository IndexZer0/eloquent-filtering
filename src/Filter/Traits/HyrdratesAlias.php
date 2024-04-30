<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits;

use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

trait HyrdratesAlias
{
    protected function hydrateAlias(PendingFilter $pendingFilter, ?string $alias): PendingFilter
    {
        $filterFqcn = $pendingFilter->filterFqcn();
        $data = $pendingFilter->data();

        if ($alias !== null) {
            $data[$filterFqcn::targetKey()] = $this->alias;
        }

        return $pendingFilter->withData($data);
    }
}
