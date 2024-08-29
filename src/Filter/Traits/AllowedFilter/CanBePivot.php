<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\AllowedFilter;

trait CanBePivot
{
    protected ?string $pivotTable = null;

    public function pivot(string $pivotTable): self
    {
        $this->pivotTable = $pivotTable;
        return $this;
    }

    public function isPivot(): bool
    {
        return $this->pivotTable !== null;
    }

    public function getPivotTable(): string
    {
        return $this->pivotTable;
    }
}
