<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Utilities;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

class RelationUtils
{
    public static function relationMethodExists(string $modelFqcn, string $relationMethodName): bool
    {
        $relationMethods = self::getRelationMethodNames($modelFqcn);
        return $relationMethods->contains($relationMethodName);
    }

    public static function getRelationMethodNames(string $modelFqcn): Collection
    {
        $methods = (new ReflectionClass($modelFqcn))->getMethods();

        return collect($methods)
            ->filter(function (ReflectionMethod $method) {
                $returnType = $method->getReturnType();

                if (!($returnType instanceof ReflectionNamedType)) {
                    return false;
                }

                return str_contains($returnType->getName(), 'Illuminate\Database\Eloquent\Relations');
            })
            ->map(fn (ReflectionMethod $method) => $method->name)
            ->values();
    }

    public static function getRelationModels(string $modelFqcn, string $relationMethodName): RelationModels
    {
        return new RelationModels($modelFqcn, $relationMethodName);
    }

    /*
     * \Illuminate\Database\Eloquent\Relations\Relation::getMorphAlias()
     * was added in laravel/framework v11.11.0
     * https://github.com/laravel/framework/releases/tag/v11.11.0
     *
     * RelationUtils::getMorphAlias() exists so that this package can support
     * the same functionality for laravel 10.x
     *
     * Though this may not be needed and could instead just do:
     * (new $className())->getMorphClass()
     */
    public static function getMorphAlias(string $className)
    {
        return array_search($className, Relation::$morphMap, strict: true) ?: $className;
    }

    public static function existsInMorphMap(string $className): bool
    {
        return collect(Relation::$morphMap)->containsStrict($className);
    }
}
