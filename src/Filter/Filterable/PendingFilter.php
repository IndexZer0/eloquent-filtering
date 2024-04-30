<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\AppliesToTarget;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;

class PendingFilter
{
    public function __construct(
        protected string $type,
        protected array $data,
        protected string $filterFqcn,
        protected FilterableList $filterableList = new AllFiltersAllowed(),
    ) {
    }

    public function type(): string
    {
        return $this->type;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function filterFqcn(): string
    {
        return $this->filterFqcn;
    }

    public function filterableList(): FilterableList
    {
        return $this->filterableList;
    }

    public function usage(): string
    {
        return $this->filterFqcn::usage();
    }

    public function target(): string
    {
        return data_get($this->data, $this->filterFqcn::targetKey());
    }

    public function getDeniedMessage(): string
    {
        $message ="\"{$this->type}\" filter%s is not allowed";

        $target = is_a($this->filterFqcn, AppliesToTarget::class, true) ? $this->target() : null;

        return sprintf($message, $target ? " for \"{$target}\"" : '');
    }

    public function withFilterableList(FilterableList $filterableList): PendingFilter
    {
        return new PendingFilter(
            $this->type,
            $this->data,
            $this->filterFqcn,
            $filterableList,
        );
    }

    public function withData(array $data): PendingFilter
    {
        return new PendingFilter(
            $this->type,
            $data,
            $this->filterFqcn,
            $this->filterableList,
        );
    }

    public function createFilter(): FilterMethod
    {
        return new $this->filterFqcn(...$this->data);
    }
}
