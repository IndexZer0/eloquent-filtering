<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Suppression\Suppression;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Exceptions\CustomException;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

afterEach(function (): void {
    Suppression::clearSuppressionHandler();
});

it('can specify suppression handler', function (): void {

    Suppression::handleUsing(function (SuppressibleException $se): void {
        throw new CustomException();
    });

    Author::filter(
        [
            [
                'type' => '$this-filter-does-not-exist',
            ],
        ],
        Filter::all()
    );

})->throws(CustomException::class);
