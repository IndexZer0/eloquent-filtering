<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Event;

it('can have validation rules', function (): void {

    Event::filter(
        [
            [
                'target' => 'starting_at',
                'type'   => '$between',
                'value'  => [
                    '2024-01-05', '2024-01-01',
                ],
            ],
        ],
        Filter::only(
            Filter::field('starting_at', [FilterType::BETWEEN->withRules([
                'value.0' => ['date', 'before:value.1'],
                'value.1' => ['date', 'after:value.0'],
            ])]),
        )
    );

})->throws(MalformedFilterFormatException::class, '"$between" filter does not match required format.');
