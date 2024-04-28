<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractRelationshipFilter;

class DoesntHasFilter extends AbstractRelationshipFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$doesntHas';
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereDoesntHave($this->target, function (Builder $query): void {

            /** @var FilterApplier $filterApplier */
            $filterApplier = resolve(FilterApplier::class);
            $filterApplier->apply($query, $this->value);

        });
    }
}
