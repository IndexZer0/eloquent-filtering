<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MissingFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters\KebabCaseFilter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    Author::create([
        'id'   => 1,
        'name' => 'This Is A Name',
    ]);
    Author::create([
        'id'   => 2,
        'name' => 'This Is Another Name',
    ]);
});

it('custom filter must be in config file', function (): void {

    config()->set('eloquent-filtering.custom_filters', []);

    Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$kebabCase',
                'value'  => 'This is A name',
            ],
        ],
        Filter::only(
            Filter::field('name', ['$kebabCase']),
        ),
    );

})->throws(MissingFilterException::class, 'Can not find filter for "$kebabCase"');

it('can perform a custom "field" filter | $kebabCase', function (): void {

    config()->set('eloquent-filtering.custom_filters', [KebabCaseFilter::class]);

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$kebabCase',
                'value'  => 'This is A name',
            ],
        ],
        Filter::only(
            Filter::field('name', ['$kebabCase']),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where LOWER(REPLACE(authors.name, ' ', '-')) = 'this-is-a-name'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1)
        ->and($models->first()->name)->toBe('This Is A Name');

});
