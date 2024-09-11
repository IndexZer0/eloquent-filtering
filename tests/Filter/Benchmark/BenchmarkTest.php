<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Benchmark;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('benchmarks', function (): void {
    $queryBuilderFn = fn () => Author::query()
        ->where('authors.name', 'George Raymond Richard Martin')
        ->whereHas('books', function (Builder $query): void {
            $query->where('books.title', 'A Game of Thrones')
                ->where(function (Builder $query): void {
                    $query->orWhere(function ($query): void {
                        $query->where('books.description', 'LIKE', '%A Game of Thrones%');
                    })->orWhere(function ($query): void {
                        $query->where('books.description', 'LIKE', '%Song of Ice and Fire%');
                    });
                })
                ->whereHas('comments', function (Builder $query): void {
                    $query->where('comments.content', 'Thanks D&D :S');
                });
        });

    $eloquentFilteringFn = fn () => Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
            [
                'target' => 'books',
                'type'   => '$has',
                'value'  => [
                    [
                        'target' => 'title',
                        'type'   => '$eq',
                        'value'  => 'A Game of Thrones',
                    ],
                    [
                        'type'  => '$or',
                        'value' => [
                            [
                                'target' => 'description',
                                'type'   => '$like',
                                'value'  => 'A Game of Thrones',
                            ],
                            [
                                'target' => 'description',
                                'type'   => '$like',
                                'value'  => 'Song of Ice and Fire',
                            ],
                        ],
                    ],
                    [
                        'type'   => '$has',
                        'target' => 'comments',
                        'value'  => [
                            [
                                'target' => 'content',
                                'type'   => '$eq',
                                'value'  => 'Thanks D&D :S',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::EQUAL]),
            Filter::relation(
                'books',
                [FilterType::HAS],
                Filter::only(
                    Filter::field('title', [FilterType::EQUAL]),
                    Filter::field('description', [FilterType::LIKE]),
                    Filter::relation(
                        'comments',
                        [FilterType::HAS],
                        Filter::only(
                            Filter::field('content', [FilterType::EQUAL])
                        )
                    )
                )
            )
        )
    );

    expect($queryBuilderFn()->toRawSql())->toBe($eloquentFilteringFn()->toRawSql());

    /*Benchmark::dd([
        'QueryBuilder' => $queryBuilderFn, // 0.360ms
        'EloquentFiltering' => $eloquentFilteringFn, // 2.867ms
    ], iterations: 10000);*/

});
