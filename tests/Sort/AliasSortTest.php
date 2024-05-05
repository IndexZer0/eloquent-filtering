<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Sort\Sortable\Sort;
use IndexZer0\EloquentFiltering\Target\Target;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\AuthorProfile;

beforeEach(function (): void {
    Author::create([
        'id'   => 1,
        'name' => 'Name 1',
    ]);
    AuthorProfile::create([
        'age'       => 20,
        'author_id' => 1,
    ]);

    Author::create([
        'id'   => 2,
        'name' => 'Name 2',
    ]);
    AuthorProfile::create([
        'age'       => 30,
        'author_id' => 2,
    ]);

    Author::create([
        'id'   => 3,
        'name' => 'Name 3',
    ]);
    AuthorProfile::create([
        'age'       => 40,
        'author_id' => 3,
    ]);
});

it('can alias field | Sort::field', function (): void {

    $query = Author::sort(
        [
            [
                'target' => 'name',
                'value'  => 'desc',
            ],
        ],
        Sort::only(
            Sort::field(Target::alias('name', 'name_alias'))
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" order by "name_alias" desc
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

});


it('can alias field | Sort::all()', function (): void {

    $query = Author::sort(
        [
            [
                'target' => 'name',
                'value'  => 'desc',
            ],
        ],
        Sort::all(
            Target::alias('name', 'name_alias')
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" order by "name_alias" desc
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

});
