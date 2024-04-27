<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use IndexZer0\EloquentFiltering\Rules\Scalar;

readonly class LikeFilter extends WhereFilter
{
    protected function valueBefore(): string
    {
        return '%';
    }

    protected function valueAfter(): string
    {
        return '%';
    }

    protected function operatorPrefix(): string
    {
        return '';
    }

    public static function type(): string
    {
        return '$like';
    }

    protected function operator(): string
    {
        if ($this->operatorPrefix() === '') {
            return 'LIKE';
        }
        return "{$this->operatorPrefix()} LIKE";
    }

    protected function value(): string
    {
        return "{$this->valueBefore()}{$this->value}{$this->valueAfter()}";
    }

    public static function format(): array
    {
        return [
            'target' => ['required', 'string'],
            'value'  => ['required', new Scalar()],
        ];
    }

    public function target(): string
    {
        return $this->target;
    }

    public function hasTarget(): true
    {
        return true;
    }
}
