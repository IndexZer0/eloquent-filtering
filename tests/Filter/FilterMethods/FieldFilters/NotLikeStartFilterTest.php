<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    Author::create([
        'id'   => 1,
        'name' => '__text__',
    ]);
    Author::create([
        'id'   => 2,
        'name' => '__text',
    ]);
    Author::create([
        'id'   => 3,
        'name' => 'text__',
    ]);
    Author::create([
        'id'   => 4,
        'name' => 'other',
    ]);
});

it('can perform $notLike:start filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$notLike:start',
                'value'  => 'text',
            ],
        ],
        Filter::only(
            Filter::field('name', ['$notLike:start']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" NOT LIKE 'text%'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(3)
        ->and($models->pluck('name')->toArray())->toBe(['__text__', '__text', 'other']);

});
