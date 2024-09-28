<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Utilities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;

class RelationModels
{
    protected Relation $query;

    public function __construct(string $modelFqcn, string $relationMethodName)
    {
        $model = new $modelFqcn();

        $this->query = $model->$relationMethodName();
    }

    public function getAll(): array
    {
        $models = [$this->getRelated()];

        if ($this->query instanceof BelongsToMany) {
            $pivotClass = $this->query->getPivotClass();
            $models[] = new $pivotClass();
        }

        return $models;
    }

    public function getRelated(): Model
    {
        return $this->query->getRelated();
    }
}
