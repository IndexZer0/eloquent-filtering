<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Manufacturer;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Product;

beforeEach(function (): void {
    Manufacturer::create([
        'id' => 1,
        'name' => 'Manufacturer 1'
    ]);
    Product::create([
        'id'   => 1,
        'manufacturer_id' => 1,
        'name' => 'Product 1',
    ]);

    Manufacturer::create([
        'id' => 2,
        'name' => 'Manufacturer 2'
    ]);
    Product::create([
        'id'   => 2,
        'manufacturer_id' => 2,
        'name' => 'Product 2',
    ]);
});

it('uses allowed filters from model as default', function (): void {

    config()->set('eloquent-filtering.suppress.filter.denied', true);

    $query = Product::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'Product 1',
            ],
            [
                'target' => 'description',
                'type'   => '$eq',
                'value'  => 'Product 1',
            ],
        ]
    );

    $expectedSql = <<< SQL
        select * from "products" where "name" = 'Product 1'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1);

});
