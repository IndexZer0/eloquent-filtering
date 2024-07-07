<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AppliesToTarget;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;
use IndexZer0\EloquentFiltering\Filter\RequestedFilter;

class PendingFilter
{
    protected array $validatedData = [];

    public function __construct(
        protected RequestedFilter $requestedFilter,
        protected string $filterFqcn,
        protected array  $data,
    ) {
    }

    public function requestedFilter(): RequestedFilter
    {
        return $this->requestedFilter;
    }

    public function type(): string
    {
        return $this->requestedFilter->type;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function filterFqcn(): string
    {
        return $this->filterFqcn;
    }

    public function is(FilterContext $context): bool
    {
        return $this->filterFqcn::context() === $context;
    }

    public function desiredTarget(): ?string
    {
        if (is_a($this->filterFqcn, AppliesToTarget::class, true)) {
            return data_get($this->data, $this->filterFqcn::targetKey());
        }

        return null;
    }

    public function getDeniedMessage(): string
    {
        $message = "\"{$this->requestedFilter->fullTypeString()}\" filter%s is not allowed";

        $target = is_a($this->filterFqcn, AppliesToTarget::class, true) ? $this->desiredTarget() : null;

        return sprintf($message, $target ? " for \"{$target}\"" : '');
    }

    public function validateWith(array $rules): void
    {
        try {
            $this->validatedData = Validator::validate(
                $this->data,
                array_merge_recursive($this->filterFqcn::format(), $rules),
            );
        } catch (ValidationException $ve) {
            throw new MalformedFilterFormatException($this->requestedFilter->fullTypeString(), $ve);
        }
    }

    public function approveWith(
        ?Target           $target = null,
        ?FilterCollection $childFilters = null
    ): ApprovedFilter {
        return new ApprovedFilter(
            $this->filterFqcn,
            $this->validatedData,
            $this->requestedFilter->modifiers,
            $target,
            $childFilters
        );
    }
}
