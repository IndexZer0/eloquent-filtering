<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedMorphType;
use IndexZer0\EloquentFiltering\Filter\Context\EloquentContext;
use IndexZer0\EloquentFiltering\Filter\Filterable\NoFiltersAllowed;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\InFilter;
use IndexZer0\EloquentFiltering\Filter\RequestedFilter;
use IndexZer0\EloquentFiltering\Target\JsonPathTarget;
use IndexZer0\EloquentFiltering\Target\Target;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\WhereFilter;

// The tests in this file is to get to 100% code-coverage.
// These are needed because there are some paths through the
// class hierarchies that are not possible in normal usage.

it('has empty allowed fields and allowed relations for no filters allowed', function (): void {

    $noFiltersAllowed = new NoFiltersAllowed();

    expect($noFiltersAllowed->getAllowedFields())->toBe([])
        ->and($noFiltersAllowed->getAllowedRelations())->toBe([]);

});

it('throws Exception when calling getAllowedType() on AllowedMorphType', function (): void {

    $allowedMorphType = new AllowedMorphType(
        Target::alias('something'),
        new NoFiltersAllowed(),
    );

    $allowedMorphType->getAllowedType(new PendingFilter(
        RequestedFilter::fromString('$in:null'),
        InFilter::class,
        [
            'target' => 'something',
            'type'   => '$in:null',
            'value'  => [
                1, 2, 3, null,
            ],
        ],
        new Author(),
        relation: null,
        previousPendingFilter: null,
        index: 1,
    ));

})->throws(Exception::class, "Not implemented");

it('returns target from JsonPathTarget', function (): void {

    $jsonPathTarget = new JsonPathTarget('array->something');

    expect($jsonPathTarget->getReal())->toBe('array->something');

});

it('has = as default operator', function (): void {

    // This test is to make WhereFilter::operator() run for coverage.

    class FakeFilter extends WhereFilter
    {
        public static function type(): string
        {
            return '$fake';
        }
    }

    $fakeFilter = new FakeFilter(1);
    $fakeFilter->setEloquentContext(new EloquentContext(
        new Author()
    ));
    $fakeFilter->setTarget('name');

    $query = Author::query();
    $fakeFilter->apply($query);
    $sql = $query->toRawSql();

    expect($sql)->toBe('select * from "authors" where "authors"."name" = 1');

});
