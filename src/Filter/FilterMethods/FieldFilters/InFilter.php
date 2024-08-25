<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\Modifiable;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\Targetable;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables\HasModifiers;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext\FieldFilter;
use IndexZer0\EloquentFiltering\Rules\NullableWhereValue;

class InFilter implements FilterMethod, Modifiable, Targetable
{
    use FieldFilter;
    use HasModifiers;

    public function __construct(
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
        return FilterType::IN->value;
    }

    public static function format(): array
    {
        return [
            'value'   => ['required', 'array', 'min:1'],
            'value.*' => [new NullableWhereValue()],
        ];
    }

    public function apply(Builder $query): Builder
    {
        $value = collect($this->value);
        $valueContainNull = $value->containsStrict(null);
        $hasNullModifier = $this->hasModifier('null');

        $target = $this->eloquentContext->qualifyColumn($this->target);

        return $query->whereIn(
            $target,
            $value->filter(fn ($item) => $item !== null),
            not: $this->not()
        )->when($valueContainNull && $hasNullModifier, function (Builder $query) use ($target): void {
            $query->whereNull(
                $target,
                $this->not() ? 'and' : 'or',
                $this->not()
            );
        });
    }

    public static function supportedModifiers(): array
    {
        return ['null'];
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
