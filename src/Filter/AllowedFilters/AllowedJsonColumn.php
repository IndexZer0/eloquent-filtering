<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

class AllowedJsonColumn extends AllowedColumn
{
    public function matches(PendingFilter $pendingFilter): bool
    {
        if ($pendingFilter->usage() !== FilterMethod::USAGE_JSON_COLUMN) {
            return false;
        }

        return in_array($pendingFilter->type(), $this->types, true) &&
            $this->target === $pendingFilter->target();
    }
}
