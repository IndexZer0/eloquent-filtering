<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Utilities;

use Illuminate\Database\Eloquent\Model;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Exceptions\InvalidModelFqcnException;
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

    public static function modelIsFilterable(Model $model): bool
    {
        return is_a($model, IsFilterable::class);
    }

    public static function ensureFqcnIsModel(string $fqcn): void
    {
        if (!is_a($fqcn, Model::class, true)) {
            throw new InvalidModelFqcnException('Must be an eloquent model fully qualified class name.');
        }
    }

    public static function getModelsAllowedFilters(Model $model): ?AllowedFilterList
    {
        if (!self::modelIsFilterable($model)) {
            return null;
        }

        /** @var IsFilterable $model */
        return $model->allowedFilters();
    }
}
