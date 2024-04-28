<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $notIn filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$notIn',
                'value'  => [
                    'J. K. Rowling',
                    'William Shakespeare',
                ],
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$notIn']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" not in ('J. K. Rowling', 'William Shakespeare')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});
