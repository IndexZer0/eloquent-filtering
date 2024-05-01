<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Helpers\JsonPath;

class AllowedJsonColumn extends AllowedColumn
{
    public function matches(PendingFilter $pendingFilter): bool
    {
        if ($pendingFilter->usage() !== FilterMethod::USAGE_JSON_COLUMN) {
            return false;
        }

        if (!in_array($pendingFilter->type(), $this->types, true)) {
            return false;
        }

        return $this->targetMatches($pendingFilter->target());
    }

    private function targetMatches(string $target): bool
    {
        $jsonPath = JsonPath::of($this->target);
        return $jsonPath->allows($target);
    }
}
