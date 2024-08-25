<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $notEq filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$notEq',
                'value'  => 'J. R. R. Tolkien',
            ],
        ],
        Filter::only(
            Filter::field('name', ['$notEq']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" != 'J. R. R. Tolkien'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1);

});
