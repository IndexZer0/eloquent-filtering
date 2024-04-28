<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Product;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Manufacturer;

beforeEach(function (): void {
    $this->createManufacturers();
});

it('TODO', function (): void {

    $query = Product::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'Product 1',
            ],
            [
                'target' => 'manufacturer',
                'type'   => '$has',
                'value'  => [
                    [
                        'target' => 'name',
                        'type'   => '$eq',
                        'value'  => 'Manufacturer 1',
                    ],
                ],
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$eq']),
            /*Filter::relationWithDefaults(
                'manufacturer',
                ['$has'],
                Manufacturer::class,
            )*/
            /*Filter::relation(
                'manufacturer',
                ['$has'],
            )*/
        )
    );

    $expectedSql = <<< SQL
        select * from "products" where "name" = 'Product 1' and exists (select * from "manufacturers" where "products"."manufacturer_id" = "manufacturers"."id" and "name" = 'Manufacturer 1')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1);
})->skip();
