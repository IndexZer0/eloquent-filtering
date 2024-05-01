<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $doesntHas filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'books',
                'type'   => '$doesntHas',
                'value'  => [],
            ],
        ],
        Filter::only(
            Filter::relation('books', ['$doesntHas']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where not exists (select * from "books" where "authors"."id" = "books"."author_id")
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

});
