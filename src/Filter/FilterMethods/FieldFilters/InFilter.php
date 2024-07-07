<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractFieldFilter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\HasModifiers;
use IndexZer0\EloquentFiltering\Rules\NullableWhereValue;
use IndexZer0\EloquentFiltering\Rules\TargetRules;

class InFilter extends AbstractFieldFilter
{
    use HasModifiers;

    final public function __construct(
        protected string $target,
        protected array $value,
        protected array $modifiers
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
            ...TargetRules::get(),
            'value'   => ['required', 'array', 'min:1'],
            'value.*' => [new NullableWhereValue()],
        ];
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static(
            $approvedFilter->target()->getReal(),
            $approvedFilter->data_get('value'),
            $approvedFilter->modifiers(),
        );
    }

    public function apply(Builder $query): Builder
    {
        $value = collect($this->value);
        $valueContainNull = $value->containsStrict(null);
        $hasNullModifier = $this->hasModifier('null');

        return $query->whereIn($this->target, $value->filter(fn ($item) => $item !== null), not: $this->not())
            ->when($valueContainNull && $hasNullModifier, function (Builder $query): void {
                $query->whereNull($this->target, $this->not() ? 'and' : 'or', $this->not());
            });
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
