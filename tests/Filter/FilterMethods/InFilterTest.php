<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $in filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$in',
                'value'  => [
                    'George Raymond Richard Martin',
                    'J. R. R. Tolkien',
                ],
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$in']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" in ('George Raymond Richard Martin', 'J. R. R. Tolkien')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});
