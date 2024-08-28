<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\CustomFilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\RequestedFilter;
use IndexZer0\EloquentFiltering\Utilities\ClassUtils;

class PendingFilter
{
    public function __construct(
        protected RequestedFilter $requestedFilter,
        protected string $filterFqcn,
        protected array  $data,
        protected Model $model,
        protected ?Relation $relation = null,
    ) {
    }

    public function requestedFilter(): RequestedFilter
    {
        return $this->requestedFilter;
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
        return data_get($this->data, 'target');
    }

    public function getDeniedMessage(): string
    {
        $messageParts = collect([
            "\"{$this->requestedFilter->fullTypeString()}\" filter",
        ]);

        if (($target = $this->desiredTarget()) !== null) {
            $messageParts->push("for \"{$target}\"");
        }

        $messageParts->push("is not allowed");

        return $messageParts->join(' ');
    }

    public function validate(array $rules = []): void
    {
        try {
            Validator::validate(
                $this->data,
                count($rules) > 0 ? $rules : $this->getFilterMethodRules(),
            );
        } catch (ValidationException $ve) {
            throw MalformedFilterFormatException::withMessages([
                'filter' => ["\"{$this->requestedFilter->fullTypeString()}\" filter does not match required format."],
                ...$ve->errors(),
            ]);
        }
    }

    protected function getFilterMethodRules(): array
    {
        $rules = $this->filterFqcn::format();

        foreach (class_uses_recursive($this->filterFqcn) as $trait) {
            $rulesMethod = Str::lcfirst(class_basename($trait)) . 'Rules';

            if (method_exists($this->filterFqcn, $rulesMethod)) {
                $rules = array_merge_recursive($rules, $this->filterFqcn::$rulesMethod());
            }
        }

        return $rules;
    }

    public function getCustomFilterParser(): CustomFilterParser
    {
        return $this->filterFqcn::customFilterParser();
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function relation(): ?Relation
    {
        return $this->relation;
    }

    protected function getFilterConstructorParameters(): array
    {
        return collect($this->data)->only(
            ClassUtils::getClassConstructorParameterNames($this->filterFqcn)
        )->toArray();
    }

    public function createFilter(): FilterMethod
    {
        $filterFqcn = $this->filterFqcn;

        $filterMethod = new $filterFqcn(
            ...$this->getFilterConstructorParameters()
        );

        return $filterMethod;
    }
}
