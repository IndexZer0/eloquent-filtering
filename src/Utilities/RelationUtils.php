<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Utilities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionMethod;

class RelationUtils
{
    public static function relationMethodExists(string $relationMethod, string $modelFqcn): bool
    {
        $relationMethods = self::getRelationMethodNames($modelFqcn);
        return $relationMethods->contains($relationMethod);
    }

    public static function getRelationMethodNames(string $modelFqcn): Collection
    {
        $methods = (new ReflectionClass($modelFqcn))->getMethods();

        return collect($methods)
            ->filter(function (ReflectionMethod $method) {
                if (($returnType = $method->getReturnType()) === null) {
                    return false;
                }
                return str_contains($returnType->__toString(), 'Illuminate\Database\Eloquent\Relations');
            })
            ->map(fn (ReflectionMethod $method) => $method->name)
            ->values();
    }

    public static function getRelationModel(string $modelFqcn, string $relationName): Model
    {
        $model = new $modelFqcn();

        /** @var Relation $query */
        $query = $model->$relationName();

        return $query->getModel();
    }
}
