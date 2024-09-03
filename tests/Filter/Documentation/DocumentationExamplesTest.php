<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Documentation\Package;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Comment;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\Image;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Person;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Product;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Project;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\User;

beforeEach(function (): void {
    $this->createPackages();
    $this->createMorphRecords();
});

it('EqualFilter | $eq', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$eq',
            'target' => 'name',
            'value'  => 'Taylor',
        ],
    ], Filter::only(
        Filter::field('name', [FilterType::EQUAL])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."name" = 'Taylor'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotEqualFilter | $notEq', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$notEq',
            'target' => 'name',
            'value'  => 'Taylor',
        ],
    ], Filter::only(
        Filter::field('name', [FilterType::NOT_EQUAL])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."name" != 'Taylor'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('GreaterThanFilter | $gt', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$gt',
            'target' => 'age',
            'value'  => 18,
        ],
    ], Filter::only(
        Filter::field('age', [FilterType::GREATER_THAN])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."age" > 18
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('GreaterThanEqualToFilter | $gte', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$gte',
            'target' => 'age',
            'value'  => 18,
        ],
    ], Filter::only(
        Filter::field('age', [FilterType::GREATER_THAN_EQUAL_TO])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."age" >= 18
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('LessThanFilter | $lt', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$lt',
            'target' => 'age',
            'value'  => 18,
        ],
    ], Filter::only(
        Filter::field('age', [FilterType::LESS_THAN])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."age" < 18
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('LessThanEqualToFilter | $lte', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$lte',
            'target' => 'age',
            'value'  => 18,
        ],
    ], Filter::only(
        Filter::field('age', [FilterType::LESS_THAN_EQUAL_TO])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."age" <= 18
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('LikeFilter | $like', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$like',
            'target' => 'description',
            'value'  => 'Laravel',
        ],
    ], Filter::only(
        Filter::field('description', [FilterType::LIKE])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "projects"."description" LIKE '%Laravel%'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('LikeFilter | $like:start', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$like:start',
            'target' => 'description',
            'value'  => 'Laravel',
        ],
    ], Filter::only(
        Filter::field('description', [FilterType::LIKE])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "projects"."description" LIKE 'Laravel%'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('LikeFilter | $like:end', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$like:end',
            'target' => 'description',
            'value'  => 'Laravel',
        ],
    ], Filter::only(
        Filter::field('description', [FilterType::LIKE])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "projects"."description" LIKE '%Laravel'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotLikeFilter | $notLike', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$notLike',
            'target' => 'description',
            'value'  => 'Symfony',
        ],
    ], Filter::only(
        Filter::field('description', [FilterType::NOT_LIKE])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "projects"."description" NOT LIKE '%Symfony%'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotLikeFilter | $notLike:start', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$notLike:start',
            'target' => 'description',
            'value'  => 'Symfony',
        ],
    ], Filter::only(
        Filter::field('description', [FilterType::NOT_LIKE])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "projects"."description" NOT LIKE 'Symfony%'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotLikeFilter | $notLike:end', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$notLike:end',
            'target' => 'description',
            'value'  => 'Symfony',
        ],
    ], Filter::only(
        Filter::field('description', [FilterType::NOT_LIKE])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "projects"."description" NOT LIKE '%Symfony'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NullFilter | $null', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$null',
            'target' => 'age',
            'value'  => true,
        ],
        [
            'type'   => '$null',
            'target' => 'weight',
            'value'  => false,
        ],
    ], Filter::only(
        Filter::field('age', [FilterType::NULL]),
        Filter::field('weight', [FilterType::NULL])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."age" is null and "people"."weight" is not null
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('InFilter | $in', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$in',
            'target' => 'name',
            'value'  => ['Taylor', 'Otwell', ],
        ],
    ], Filter::only(
        Filter::field('name', [FilterType::IN]),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."name" in ('Taylor', 'Otwell')
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('InFilter | $in:null', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$in:null',
            'target' => 'name',
            'value'  => ['Taylor', 'Otwell', null, ],
        ],
    ], Filter::only(
        Filter::field('name', [FilterType::IN]),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where ("people"."name" in ('Taylor', 'Otwell') or "people"."name" is null)
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotInFilter | $notIn', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$notIn',
            'target' => 'name',
            'value'  => ['Nuno', 'Maduro', ],
        ],
    ], Filter::only(
        Filter::field('name', [FilterType::NOT_IN]),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."name" not in ('Nuno', 'Maduro')
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotInFilter | $notIn:null', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$notIn:null',
            'target' => 'name',
            'value'  => ['Nuno', 'Maduro', null],
        ],
    ], Filter::only(
        Filter::field('name', [FilterType::NOT_IN]),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."name" not in ('Nuno', 'Maduro') and "people"."name" is not null
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('BetweenFilter | $between', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$between',
            'target' => 'age',
            'value'  => [18, 65, ],
        ],
    ], Filter::only(
        Filter::field('age', [FilterType::BETWEEN]),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."age" between 18 and 65
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotBetweenFilter | $notBetween', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$notBetween',
            'target' => 'age',
            'value'  => [18, 65, ],
        ],
    ], Filter::only(
        Filter::field('age', [FilterType::NOT_BETWEEN]),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "people"."age" not between 18 and 65
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('BetweenColumnsFilter | $betweenColumns', function (): void {
    $sql = Product::filter([
        [
            'type'   => '$betweenColumns',
            'target' => 'price',
            'value'  => [
                'min_allowed_price',
                'max_allowed_price',
            ],
        ],
    ], Filter::only(
        Filter::field('price', [FilterType::BETWEEN_COLUMNS]),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "products" where "products"."price" between "products"."min_allowed_price" and "products"."max_allowed_price"
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotBetweenColumnsFilter | $notBetweenColumns', function (): void {
    $sql = Product::filter([
        [
            'type'   => '$notBetweenColumns',
            'target' => 'price',
            'value'  => [
                'min_allowed_price',
                'max_allowed_price',
            ],
        ],
    ], Filter::only(
        Filter::field('price', [FilterType::NOT_BETWEEN_COLUMNS]),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "products" where "products"."price" not between "products"."min_allowed_price" and "products"."max_allowed_price"
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('JsonContainsFilter | $jsonContains', function (): void {
    $sql = User::filter([
        [
            'type'   => '$jsonContains',
            'target' => 'options->languages',
            'value'  => 'en',
        ],
    ], Filter::only(
        Filter::field('options->languages', [FilterType::JSON_CONTAINS]),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "users" where exists (select 1 from json_each("users"."options", '$."languages"') where "json_each"."value" is 'en')
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('JsonNotContainsFilter | $jsonNotContains', function (): void {
    $sql = User::filter([
        [
            'type'   => '$jsonNotContains',
            'target' => 'options->languages',
            'value'  => 'en',
        ],
    ], Filter::only(
        Filter::field('options->languages', [FilterType::JSON_NOT_CONTAINS]),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "users" where not exists (select 1 from json_each("users"."options", '$."languages"') where "json_each"."value" is 'en')
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('JsonLengthFilter | $jsonLength', function (): void {
    $sql = User::filter([
        [
            'type'     => '$jsonLength',
            'target'   => 'options->languages',
            'operator' => '>=',
            'value'    => 2,
        ],
    ], Filter::only(
        Filter::field('options->languages', [FilterType::JSON_LENGTH]),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "users" where json_array_length("users"."options", '$."languages"') >= 2
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('HasFilter | $has', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$has',
            'target' => 'comments',
            'value'  => [
                [
                    'type'   => '$like',
                    'target' => 'content',
                    'value'  => 'awesome',
                ],
            ],
        ],
    ], Filter::only(
        Filter::relation(
            'comments',
            [FilterType::HAS, ],
            Filter::only(
                Filter::field('content', [FilterType::LIKE])
            )
        )
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where exists (select * from "comments" where "projects"."id" = "comments"."project_id" and "comments"."content" LIKE '%awesome%')
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('DoesntHasFilter | $doesntHas', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$doesntHas',
            'target' => 'comments',
            'value'  => [
                [
                    'type'   => '$like',
                    'target' => 'content',
                    'value'  => 'boring',
                ],
            ],
        ],
    ], Filter::only(
        Filter::relation(
            'comments',
            [FilterType::DOESNT_HAS, ],
            Filter::only(
                Filter::field('content', [FilterType::LIKE])
            )
        )
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where not exists (select * from "comments" where "projects"."id" = "comments"."project_id" and "comments"."content" LIKE '%boring%')
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('HasMorphFilter | $hasMorph', function (): void {
    $sql = Image::filter([
        [
            'target' => 'imageable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => '*',
                    'value' => [],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'imageable',
            [FilterType::HAS_MORPH],
            Filter::morphType('*'),
        )
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "images" where (("images"."imageable_type" = 'articles' and exists (select * from "articles" where "images"."imageable_id" = "articles"."id")) or ("images"."imageable_type" = 'user_profiles' and exists (select * from "user_profiles" where "images"."imageable_id" = "user_profiles"."id")))
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('DoesntHasMorphFilter | $doesntHasMorph', function (): void {
    $sql = Image::filter([
        [
            'target' => 'imageable',
            'type'   => '$doesntHasMorph',
            'types'  => [
                [
                    'type'  => '*',
                    'value' => [],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'imageable',
            [FilterType::DOESNT_HAS_MORPH],
            Filter::morphType('*'),
        )
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "images" where (("images"."imageable_type" = 'articles' and not exists (select * from "articles" where "images"."imageable_id" = "articles"."id")) or ("images"."imageable_type" = 'user_profiles' and not exists (select * from "user_profiles" where "images"."imageable_id" = "user_profiles"."id")))
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('OrFilter | $or', function (): void {
    $sql = Comment::filter([
        [
            'type'  => '$or',
            'value' => [
                [
                    'type'   => '$like',
                    'target' => 'content',
                    'value'  => 'awesome',
                ],
                [
                    'type'   => '$like',
                    'target' => 'content',
                    'value'  => 'boring',
                ],
            ],
        ],
    ], Filter::only(
        Filter::field('content', [FilterType::LIKE])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "comments" where (("comments"."content" LIKE '%awesome%') or ("comments"."content" LIKE '%boring%'))
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('AndFilter | $and', function (): void {
    $sql = Comment::filter([
        [
            'type'  => '$and',
            'value' => [
                [
                    'type'   => '$like',
                    'target' => 'content',
                    'value'  => 'is awesome',
                ],
                [
                    'type'   => '$like',
                    'target' => 'content',
                    'value'  => 'is not boring',
                ],
            ],
        ],
    ], Filter::only(
        Filter::field('content', [FilterType::LIKE])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "comments" where (("comments"."content" LIKE '%is awesome%') and ("comments"."content" LIKE '%is not boring%'))
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('can filter', function (): void {
    $package = Package::filter([[
        'target' => 'name',
        'type'   => '$eq',
        'value'  => 'eloquent-filtering',
    ]])->first();

    expect($package->description)
        ->toBe('Easily filter eloquent models using arrays');
});
