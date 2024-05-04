<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractFieldFilter;

class BetweenColumnsFilter extends AbstractFieldFilter
{
    public function __construct(
        protected Target $target,
        protected array $value,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$betweenColumns';
    }

    public static function format(): array
    {
        return [
            'target'  => ['required', 'string'],
            'value'   => ['required', 'array', 'size:2'],
            'value.*' => ['required', 'string'],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereBetweenColumns($this->target->getReal(), $this->value, not: $this->not());
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static(
            $approvedFilter->target(),
            $approvedFilter->data_get('value'),
        );
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function not(): bool
    {
        return false;
    }
}
