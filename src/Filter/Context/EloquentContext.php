<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Context;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;

readonly class EloquentContext
{
    public function __construct(
        public Model $model,
        public ?Relation $relation = null,
        public bool $pivot = false,
    ) {
    }

    public function qualifyColumn(string $column): string
    {
        if ($this->relation instanceof BelongsToMany
            && $this->pivot
        ) {
            return $this->relation->qualifyPivotColumn($column);
        }
        return $this->model->qualifyColumn($column);
    }
}
