<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\AppliesToTarget;
use IndexZer0\EloquentFiltering\Filter\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;

class PendingFilter
{
    public function __construct(
        protected string $type,
        protected string $filterFqcn,
        protected array  $data,
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

    public function is(string $usage): bool
    {
        return $this->filterFqcn::usage() === $usage;
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
        $message = "\"{$this->type}\" filter%s is not allowed";

        $target = is_a($this->filterFqcn, AppliesToTarget::class, true) ? $this->desiredTarget() : null;

        return sprintf($message, $target ? " for \"{$target}\"" : '');
    }

    public function approveWith(
        ?Target           $target = null,
        ?FilterCollection $childFilters = null
    ): ApprovedFilter {
        return new ApprovedFilter(
            $this->filterFqcn,
            $this->data,
            $target,
            $childFilters
        );
    }
}
