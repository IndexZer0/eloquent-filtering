<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DuplicateFiltersException;
use IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters\DuplicateFilter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('exceptions when duplicate filter is registered', function (): void {

    config()->set('eloquent-filtering.custom_filters', [DuplicateFilter::class]);

    Author::filter(
        [],
    );

})->throws(DuplicateFiltersException::class, "Filters with the following types have been registered more than once: \"\$eq\"");
