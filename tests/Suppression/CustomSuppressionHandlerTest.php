<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Sort\Sortable\Sort;
use IndexZer0\EloquentFiltering\Suppression\Suppression;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Exceptions\CustomException;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

afterEach(function (): void {
    Suppression::clearSuppressionHandlers();
});

it('can specify invalid filter handler', function (): void {

    Suppression::handleInvalidFilterUsing(function (SuppressibleException $se): void {
        throw new CustomException();
    });

    Author::filter(
        [
            [

            ],
        ],
        Filter::all()
    );

})->throws(CustomException::class);

it('can specify missing filter handler', function (): void {

    Suppression::handleMissingFilterUsing(function (SuppressibleException $se): void {
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

it('can specify malformed filter handler', function (): void {

    Suppression::handleMalformedFilterUsing(function (SuppressibleException $se): void {
        throw new CustomException();
    });

    Author::filter(
        [
            [
                'type' => '$eq',
            ],
        ],
        Filter::all()
    );

})->throws(CustomException::class);

it('can specify denied filter handler', function (): void {

    Suppression::handleDeniedFilterUsing(function (SuppressibleException $se): void {
        throw new CustomException();
    });

    Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::none()
    );

})->throws(CustomException::class);

it('can specify filter handler', function (): void {

    Suppression::handleFilterUsing(function (SuppressibleException $se): void {
        throw new CustomException($se->suppressionKey());
    });

    // Invalid filter
    try {
        Author::filter(
            [
                [

                ],
            ],
            Filter::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.filter.invalid", $ce->getMessage());
    }

    // Missing filter
    try {
        Author::filter(
            [
                [
                    'type' => '$this-filter-does-not-exist',
                ],
            ],
            Filter::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.filter.missing", $ce->getMessage());
    }

    // Malformed filter
    try {
        Author::filter(
            [
                [
                    'type' => '$eq',
                ],
            ],
            Filter::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.filter.malformed_format", $ce->getMessage());
    }

    // Denied filter
    try {
        Author::filter(
            [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'George Raymond Richard Martin',
                ],
            ],
            Filter::none()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.filter.denied", $ce->getMessage());
    }

});

it('can specify malformed sort handler', function (): void {

    Suppression::handleMalformedSortUsing(function (SuppressibleException $se): void {
        throw new CustomException();
    });

    Author::sort(
        [
            [
                'target' => 'name',
            ],
        ],
        Sort::all()
    );

})->throws(CustomException::class);

it('can specify denied sort handler', function (): void {

    Suppression::handleDeniedSortUsing(function (SuppressibleException $se): void {
        throw new CustomException();
    });

    Author::sort(
        [
            [
                'target' => 'name',
                'value'  => 'desc',
            ],
        ],
        Sort::none()
    );

})->throws(CustomException::class);

it('can specify sort handler', function (): void {

    Suppression::handleSortUsing(function (SuppressibleException $se): void {
        throw new CustomException($se->suppressionKey());
    });

    // Malformed sort
    try {
        Author::sort(
            [
                [
                    'target' => 'name',
                ],
            ],
            Sort::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.sort.malformed_format", $ce->getMessage());
    }

    // Denied sort
    try {
        Author::sort(
            [
                [
                    'target' => 'name',
                    'value'  => 'desc',
                ],
            ],
            Sort::none()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.sort.denied", $ce->getMessage());
    }

});

it('can specify all handler', function (): void {

    Suppression::handleAllUsing(function (SuppressibleException $se): void {
        throw new CustomException($se->suppressionKey());
    });

    // Invalid filter
    try {
        Author::filter(
            [
                [

                ],
            ],
            Filter::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.filter.invalid", $ce->getMessage());
    }

    // Missing filter
    try {
        Author::filter(
            [
                [
                    'type' => '$this-filter-does-not-exist',
                ],
            ],
            Filter::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.filter.missing", $ce->getMessage());
    }

    // Malformed filter
    try {
        Author::filter(
            [
                [
                    'type' => '$eq',
                ],
            ],
            Filter::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.filter.malformed_format", $ce->getMessage());
    }

    // Denied filter
    try {
        Author::filter(
            [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'George Raymond Richard Martin',
                ],
            ],
            Filter::none()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.filter.denied", $ce->getMessage());
    }

    // Malformed sort
    try {
        Author::sort(
            [
                [
                    'target' => 'name',
                ],
            ],
            Sort::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.sort.malformed_format", $ce->getMessage());
    }

    // Denied sort
    try {
        Author::sort(
            [
                [
                    'target' => 'name',
                    'value'  => 'desc',
                ],
            ],
            Sort::none()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("suppress.sort.denied", $ce->getMessage());
    }

});


it('prioritises a more specific handler', function (): void {

    Suppression::handleAllUsing(function (SuppressibleException $se): void {
        throw new CustomException("all");
    });
    Suppression::handleFilterUsing(function (SuppressibleException $se): void {
        throw new CustomException("filter");
    });
    Suppression::handleInvalidFilterUsing(function (SuppressibleException $se): void {
        throw new CustomException("invalid");
    });
    Suppression::handleSortUsing(function (SuppressibleException $se): void {
        throw new CustomException("sort");
    });
    Suppression::handleDeniedSortUsing(function (SuppressibleException $se): void {
        throw new CustomException("denied");
    });

    // Invalid filter
    try {
        Author::filter(
            [
                [

                ],
            ],
            Filter::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("invalid", $ce->getMessage());
    }

    // Missing filter
    try {
        Author::filter(
            [
                [
                    'type' => '$this-filter-does-not-exist',
                ],
            ],
            Filter::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("filter", $ce->getMessage());
    }

    // Malformed filter
    try {
        Author::filter(
            [
                [
                    'type' => '$eq',
                ],
            ],
            Filter::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("filter", $ce->getMessage());
    }

    // Denied filter
    try {
        Author::filter(
            [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'George Raymond Richard Martin',
                ],
            ],
            Filter::none()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("filter", $ce->getMessage());
    }

    // Malformed sort
    try {
        Author::sort(
            [
                [
                    'target' => 'name',
                ],
            ],
            Sort::all()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("sort", $ce->getMessage());
    }

    // Denied sort
    try {
        Author::sort(
            [
                [
                    'target' => 'name',
                    'value'  => 'desc',
                ],
            ],
            Sort::none()
        );
        $this->fail('Should have thrown exception');
    } catch (CustomException $ce) {
        $this->assertSame("denied", $ce->getMessage());
    }

});
