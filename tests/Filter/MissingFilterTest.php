<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MissingFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('ignores missing filter when suppressed', function (): void {

    config()->set('eloquent-filtering.suppress.filter.missing', true);

    $query = Author::filter(
        [
            [
                'type' => '$this-filter-does-not-exist',
            ],
        ],
        Filter::allowAll()
    );

    $expectedSql = <<< SQL
        select * from "authors"
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});

it('errors when provided missing filter when not suppressed', function (): void {

    Author::filter(
        [
            [
                'type' => '$this-filter-does-not-exist',
            ],
        ],
        Filter::allowAll()
    );

})->throws(MissingFilterException::class, 'Can not find filter for "$this-filter-does-not-exist"');
