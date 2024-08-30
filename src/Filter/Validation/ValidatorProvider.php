<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Validation;

class ValidatorProvider
{
    public function __construct(
        protected array $rules = [],
        protected array $messages = [],
        protected array $attributes = [],
    ) {
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function merge(array|self $rules): self
    {
        $validationProvider = self::normalizeRules($rules);
        $this->rules = array_merge_recursive($this->rules, $validationProvider->getRules());
        $this->messages = array_merge($this->messages, $validationProvider->getMessages());
        $this->attributes = array_merge($this->attributes, $validationProvider->getAttributes());
        return $this;
    }

    public static function normalizeRules(array|self $rules): self
    {
        if ($rules instanceof self) {
            return $rules;
        }
        return new self($rules);
    }

    public static function from(
        array $rules = [],
        array $messages = [],
        array $attributes = [],
    ): self {
        return new self($rules, $messages, $attributes);
    }
}
