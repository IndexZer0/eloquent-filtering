<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\RelationFilters;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractRelationFilter;

class JoinFilter extends AbstractRelationFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$join';
    }

    public function apply(Builder $query): Builder
    {
        return $query->join($this->target, function (JoinClause $join): void {

            $join->on('authors.id', '=', 'books.author_id');

            /** @var FilterApplier $filterApplier */
            $filterApplier = resolve(FilterApplier::class);
            $filterApplier->apply($join, $this->value);

        });
    }
}
