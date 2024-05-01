<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Product;

beforeEach(function (): void {
    $this->createManufacturers();
});

it('can perform $notBetweenColumns filter', function (): void {
    $query = Product::filter(
        [
            [
                'target' => 'price',
                'type'   => '$notBetweenColumns',
                'value'  => [
                    'min_allowed_price',
                    'max_allowed_price',
                ],
            ],
        ],
        Filter::allowOnly(
            Filter::column('price', ['$notBetweenColumns']),
        )
    );

    $expectedSql = <<< SQL
        select * from "products" where "price" not between "min_allowed_price" and "max_allowed_price"
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

});
