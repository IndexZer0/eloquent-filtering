<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\RequiredFilters;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\DefinesAllowedChildFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\RequireableFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Exceptions\RequiredFilterException;

class RequiredFiltersChecker
{
    public function __construct(
        protected AllowedFilterList $allowedFilterList,
        protected bool $parentWasMatched,
    ) {
    }

    public function __invoke(): void
    {
        $messageBag = $this->getUnmatchedRequiredFiltersIdentifiers(
            $this->allowedFilterList,
            parentWasMatched: $this->parentWasMatched
        );
        if ($messageBag->isNotEmpty()) {
            throw RequiredFilterException::withMessages($messageBag->messages());
        }
    }

    public function getUnmatchedRequiredFiltersIdentifiers(
        AllowedFilterList $allowedFilterList,
        bool $parentWasMatched
    ): MessageBag {
        $messageBag = new MessageBag();

        $allowedFilters = $allowedFilterList->getAll();
        foreach ($allowedFilters as $allowedFilter) {
            $identifier = $allowedFilter->getIdentifier();

            if ($this->filterShouldBeAdded($allowedFilter, $parentWasMatched)) {
                $message = $allowedFilter->getRequiredMessage() ??
                    collect([Str::ucfirst($identifier), 'filter is required.'])->join(' ');

                $messageBag->add(
                    $identifier,
                    $message
                );
            }

            $beenMatched = $allowedFilter->hasBeenMatched();

            if ($allowedFilter instanceof DefinesAllowedChildFilters) {
                $messageBag = $messageBag->merge(
                    collect(
                        $this->getUnmatchedRequiredFiltersIdentifiers(
                            $allowedFilter->allowedFilters(),
                            $beenMatched
                        )->getMessages()
                    )
                        ->mapWithKeys(fn ($value, $key) => ["{$identifier}.{$key}" => $value])
                        ->toArray()
                );
            }
        }

        return $messageBag;
    }

    protected function filterShouldBeAdded(AllowedFilter $allowedFilter, bool $parentWasMatched): bool
    {
        return $allowedFilter instanceof RequireableFilter &&
            $allowedFilter->isRequired() &&
            !$allowedFilter->hasBeenMatched() &&
            (!$allowedFilter->isScoped() || $parentWasMatched);
    }
}
