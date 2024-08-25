<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Utilities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use ReflectionClass;
use ReflectionParameter;

class ClassUtils
{
    public static function getClassConstructorParameterNames(string $fqcn): array
    {
        $reflectionClass = new ReflectionClass($fqcn);

        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return [];
        }

        $constructorParameters = collect($constructor->getParameters());

        return $constructorParameters->map(
            fn (ReflectionParameter $constructorParameter) => $constructorParameter->name
        )->toArray();
    }

    public static function usesTrait(string $fqcn, string $trait): bool
    {
        return ClassUtils::getClassTraits($fqcn)->contains($trait);
    }

    public static function getClassTraits(string $fqcn): Collection
    {
        return collect(class_uses_recursive($fqcn));
    }

    public static function modelIsFilterable(Model $model): bool
    {
        return is_a($model, IsFilterable::class);
    }
}