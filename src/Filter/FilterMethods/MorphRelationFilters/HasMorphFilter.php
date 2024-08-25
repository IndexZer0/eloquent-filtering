<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\MorphRelationFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\HasMorphFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\Targetable;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext\MorphRelationFilter;

class HasMorphFilter implements FilterMethod, Targetable, HasMorphFilters
{
    use MorphRelationFilter;

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return FilterType::HAS_MORPH->value;
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereHasMorph(
            $this->target,
            $this->getTypes(),
            function (Builder $query, string $type): void {

                $childFilters = $this->getChildFilters($type);

                if ($childFilters) {
                    /** @var FilterApplier $filterApplier */
                    $filterApplier = resolve(FilterApplier::class);
                    $filterApplier->apply($query, $childFilters);
                }
            },
            $this->operator()
        );
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function operator(): string
    {
        return '>=';
    }

    protected function getTypes(): array
    {
        return $this->types->pluck('type')->toArray();
    }

    protected function getChildFilters(string $type): ?FilterCollection
    {
        $alias = Relation::getMorphAlias($type);
        return $this->getChildFiltersFor($alias);
    }

    protected function getChildFiltersFor(string $type): ?FilterCollection
    {
        return data_get($this->types->get($type), 'filters');
    }
}
