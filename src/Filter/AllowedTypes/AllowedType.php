<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedTypes;

use IndexZer0\EloquentFiltering\Filter\AllowedModifiers\SomeModifiersAllowed;
use IndexZer0\EloquentFiltering\Filter\AvailableFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedModifiers;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\Modifiable;
use IndexZer0\EloquentFiltering\Filter\Exceptions\UnsupportedModifierException;
use IndexZer0\EloquentFiltering\Filter\RequestedFilter;
use IndexZer0\EloquentFiltering\Filter\Validation\ValidatorProvider;

class AllowedType
{
    private string $filterFqcn;

    private AllowedModifiers $allowedModifiers;

    private ValidatorProvider $validatorProvider;

    public function __construct(
        public string $type,
    ) {
        $this->filterFqcn = resolve(AvailableFilters::class)->find($this->type);
        $this->allowedModifiers = new SomeModifiersAllowed(...$this->getSupportedModifiers());
        $this->validatorProvider = new ValidatorProvider();
    }

    public function withModifiers(string ...$modifiers): AllowedType
    {
        $supportedModifiers = collect($this->getSupportedModifiers());
        foreach ($modifiers as $modifier) {
            if (!$supportedModifiers->containsStrict($modifier)) {
                throw new UnsupportedModifierException("\"{$modifier}\" is not a supported modifier");
            }
        }
        $this->allowedModifiers = new SomeModifiersAllowed(...$modifiers);
        return $this;
    }

    public function withValidation(
        array $rules,
        array $messages = [],
        array $attributes = [],
    ): self {
        $this->validatorProvider = ValidatorProvider::from($rules, $messages, $attributes);
        return $this;
    }

    public function getValidatorProvider(): ValidatorProvider
    {
        return $this->validatorProvider;
    }

    public function matches(RequestedFilter $requestedFilter): bool
    {
        if ($this->type === $requestedFilter->type
            && $this->allowedModifiers->containsAll(...$requestedFilter->modifiers)
        ) {
            return true;
        }

        return false;
    }

    private function getSupportedModifiers(): array
    {
        if (is_a($this->filterFqcn, Modifiable::class, true)) {
            return $this->filterFqcn::supportedModifiers();
        }
        return [];
    }
}
