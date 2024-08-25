<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Builder;

use IndexZer0\EloquentFiltering\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\Context\EloquentContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\HasChildFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\Targetable;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\HasMorphFilters;
use IndexZer0\EloquentFiltering\Filter\Morph\MorphTypes;

class FilterBuilder
{
    protected array $builderSteps = [];

    public function __construct(protected PendingFilter $pendingFilter)
    {
    }

    public function addBuildStep(callable $fn): self
    {
        $this->builderSteps[] = $fn;
        return $this;
    }

    public function target(Target $target): self
    {
        $this->addBuildStep(function (FilterMethod $filterMethod) use ($target): void {
            if (is_a($filterMethod, Targetable::class)) {
                $filterMethod->setTarget($target->getReal());
            }
        });
        return $this;
    }

    public function childFilters(FilterCollection $filters): self
    {
        $this->addBuildStep(function (FilterMethod $filterMethod) use ($filters): void {
            if (is_a($filterMethod, HasChildFilters::class)) {
                $filterMethod->setChildFilters($filters);
            }
        });
        return $this;
    }

    public function morphTypes(MorphTypes $morphTypes): self
    {
        $this->addBuildStep(function (FilterMethod $filterMethod) use ($morphTypes): void {
            if (is_a($filterMethod, HasMorphFilters::class)) {
                $filterMethod->setMorphTypes($morphTypes);
            }
        });
        return $this;
    }

    public function build(EloquentContext $eloquentContext): FilterMethod
    {
        $filterMethod = $this->pendingFilter->createFilter($eloquentContext);

        foreach ($this->builderSteps as $builderStep) {
            $builderStep($filterMethod);
        }

        return $filterMethod;
    }
}
