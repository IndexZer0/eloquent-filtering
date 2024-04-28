<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Product;

beforeEach(function (): void {
    $this->createManufacturers();
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
