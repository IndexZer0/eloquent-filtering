<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Support\Str;

class RequestedFilter
{
    public array $modifiers;

    public function __construct(public string $type, string ...$modifiers)
    {
        $this->modifiers = $modifiers;
    }

    public static function fromString(string $type): self
    {
        $parts = Str::of($type)->explode(':');

        return new self($parts->shift(), ...$parts->toArray());
    }

    public function fullTypeString(): string
    {
        return collect([$this->type, ...$this->modifiers])->join(':');
    }
}
