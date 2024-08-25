<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Product;

beforeEach(function (): void {
    $this->createManufacturers();
});

it('can perform $betweenColumns filter', function (): void {
    $query = Product::filter(
        [
            [
                'target' => 'price',
                'type'   => '$betweenColumns',
                'value'  => [
                    'min_allowed_price',
                    'max_allowed_price',
                ],
            ],
        ],
        Filter::only(
            Filter::field('price', [FilterType::BETWEEN_COLUMNS]),
        )
    );

    $expectedSql = <<< SQL
        select * from "products" where "products"."price" between "min_allowed_price" and "max_allowed_price"
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
    ->and($models->pluck('name')->toArray())->toBe(['Product 1', 'Product 2']);

});
