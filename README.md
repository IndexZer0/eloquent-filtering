# eloquent-filtering

[![Latest Version on Packagist](https://img.shields.io/packagist/v/indexzer0/eloquent-filtering.svg?style=flat-square)](https://packagist.org/packages/indexzer0/eloquent-filtering)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/indexzer0/eloquent-filtering/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/indexzer0/eloquent-filtering/actions?query=workflow%3Arun-tests+branch%3Amain)
[![codecov](https://codecov.io/gh/IndexZer0/eloquent-filtering/graph/badge.svg?token=34B3NIPBRM)](https://codecov.io/gh/IndexZer0/eloquent-filtering)
[![Total Downloads](https://img.shields.io/packagist/dt/indexzer0/eloquent-filtering.svg?style=flat-square)](https://packagist.org/packages/indexzer0/eloquent-filtering)

---

## Simple example with relationship filter.

```php
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable

class Product extends Model
{
    use Filterable;
    
    public function manufacturer(): HasOne
    {
        return $this->hasOne(Manufacturer::class);
    }
}

$filters = [
    [
        'target' => 'name',
        'type'   => '$eq',
        'value'  => 'TV',
    ],
    [
        'target' => 'manufacturer',
        'type'   => '$has',
        'value'  => [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'Sony',
            ]                
        ]        
    ]
];

$sql = Product::filter($filters)->toRawSql();
```

```sql
SELECT *
FROM "products"
WHERE "name" = 'TV'
  AND EXISTS (SELECT *
              FROM "manufacturers"
              WHERE "products"."manufacturer_id" = "manufacturers"."id"
                AND "name" = 'Sony')
```

---

- [Simple Example](#simple-example-with-relationship-filter)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
    - [Model Trait](#model-trait)
    - [Allowing Filters](#allowing-filters)
      - [Define On Model](#define-on-model)
      - [Define In Filter](#define-in-filter)
    - [Available Filters](#available-filters)
      - [EqualFilter](#EqualFilter)
      - [NotEqualFilter](#NotEqualFilter)
      - [GreaterThanFilter](#GreaterThanFilter)
      - [GreaterThanEqualToFilter](#GreaterThanEqualToFilter)
      - [LessThanFilter](#LessThanFilter)
      - [LessThanEqualToFilter](#LessThanEqualToFilter)
      - [LikeFilter](#LikeFilter)
      - [LikeStartFilter](#LikeStartFilter)
      - [LikeEndFilter](#LikeEndFilter)
      - [NotLikeFilter](#NotLikeFilter)
      - [NotLikeStartFilter](#NotLikeStartFilter)
      - [NotLikeEndFilter](#NotLikeEndFilter)
      - [OrFilter](#OrFilter)
      - [NullFilter](#NullFilter)
      - [InFilter](#InFilter)
      - [NotInFilter](#NotInFilter)
      - [BetweenFilter](#BetweenFilter)
      - [NotBetweenFilter](#NotBetweenFilter)
      - [HasFilter](#HasFilter)
      - [DoesntHasFilter](#DoesntHasFilter)
    - [Digging Deeper](#digging-deeper)
        - [Config](#config)
        - [Custom Filters](#custom-filters)
    - [Error Handling](#error-handling)
- [Changelog](#changelog)

---

## Requirements

- PHP Version >= 8.2
- Laravel Version >= 10

---

## Installation

You can install the package via composer:

```bash
composer require indexzer0/eloquent-filtering
```

Run the `install` artisan command to publish the config and service provider:

```bash
php artisan eloquent-filtering:install
```

---

## Usage

### Model Trait

Add `Filterable` trait to the model you want to filter.

```php
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable

class Product extends Model
{
    use Filterable;
}
```

---

### Allowing Filters

By default, all filters are allowed.

You can specify specific filters in two ways:

#### Define on model.

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable

class Product extends Model
{
    use Filterable;
    
    protected function allowedFilters(): FilterableList
    {
        return Filter::allow(
            Filter::column('name', ['$eq']),
            Filter::relation(
                'manufacturer', 
                ['$has', '$doesntHas'],
                Filter::column('name', ['$like'])
            )
        );
    }
    
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
```

#### Define in `::filter()`

- Defining in `::filter()` method takes priority over `allowedFilters()` on the model.

```php
Product::filter(
    $filters,
    Filter::allow(
        Filter::column('name', ['$eq']),
        Filter::relation(
            'manufacturer', 
            ['$has', '$doesntHas'],
            Filter::column('name', ['$like'])
        )
    )
)->get();
```

---

### Available Filters

This package provides core filters that give you the ability to perform the vast majority of the filtering you'd need.

| Target       | Filter                                                | Code             | Query                                                                 |
|--------------|-------------------------------------------------------|------------------|-----------------------------------------------------------------------|
| Column       | [EqualFilter](#EqualFilter)                           | `$eq`            | `{$target} = {$value}`                                                |
| Column       | [NotEqualFilter](#NotEqualFilter)                     | `$notEq`         | `{$target} != {$value}`                                               |
| Column       | [GreaterThanFilter](#GreaterThanFilter)               | `$gt`            | `{$target} > {$value}`                                                |
| Column       | [GreaterThanEqualToFilter](#GreaterThanEqualToFilter) | `$gte`           | `{$target} >= {$value}`                                               |
| Column       | [LessThanFilter](#LessThanFilter)                     | `$lt`            | `{$target} < {$value}`                                                |
| Column       | [LessThanEqualToFilter](#LessThanEqualToFilter)       | `$lte`           | `{$target} <= {$value}`                                               |
| Column       | [LikeFilter](#LikeFilter)                             | `$like`          | `{$target} LIKE '%{$value}%'`                                         |
| Column       | [LikeStartFilter](#LikeStartFilter)                   | `$like:start`    | `{$target} LIKE '{$value}%'`                                          |
| Column       | [LikeEndFilter](#LikeEndFilter)                       | `$like:end`      | `{$target} LIKE '%{$value}'`                                          |
| Column       | [NotLikeFilter](#NotLikeFilter)                       | `$notLike`       | `{$target} NOT LIKE '%{$value}%'`                                     |
| Column       | [NotLikeStartFilter](#NotLikeStartFilter)             | `$notLike:start` | `{$target} NOT LIKE '{$value}%'`                                      |
| Column       | [NotLikeEndFilter](#NotLikeEndFilter)                 | `$notLike:end`   | `{$target} NOT LIKE '%{$value}'`                                      |
| Column       | [OrFilter](#OrFilter)                                 | `$or`            | `or`                                                                  |
| Column       | [NullFilter](#NullFilter)                             | `$null`          | `{$target} is null` <code>&#124;&#124;</code> `{$target} is not null` |
| Column       | [InFilter](#InFilter)                                 | `$in`            | `{$target} in ($value)`                                               |
| Column       | [NotInFilter](#NotInFilter)                           | `$notIn`         | `{$target} not in ($value)`                                           |
| Column       | [BetweenFilter](#BetweenFilter)                       | `$between`       | `{$target} between $value[0] and $value[1]`                           |
| Column       | [NotBetweenFilter](#NotBetweenFilter)                 | `$notBetween`    | `{$target} not between $value[0] and $value[1]`                       |
| Relationship | [HasFilter](#HasFilter)                               | `$has`           | `where exists (select * from {$target})`                              |
| Relationship | [DoesntHasFilter](#DoesntHasFilter)                   | `$doesntHas`     | `where not exists (select * from {$target})`                          |

- Accepting pull requests for more common filters.

### Filter Structure

- Filters **ALWAYS** have a `type`.
- All filters apart from `$or` have a `target`.
- Filter `value` is different depending on the filter.

#### EqualFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$eq',
        'target' => 'name',
        'value'  => 'Taylor',
    ]
])->toRawSql();
```

```sql
select * from "people" where "name" = 'Taylor'
```

#### NotEqualFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$notEq',
        'target' => 'name',
        'value'  => 'Taylor',
    ]
])->toRawSql();
```

```sql
select * from "people" where "name" != 'Taylor'
```

#### GreaterThanFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$gt',
        'target' => 'age',
        'value'  => 18,
    ]
])->toRawSql();
```

```sql
select * from "people" where "name" != 'Taylor'
```

#### GreaterThanEqualToFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$gte',
        'target' => 'age',
        'value'  => 18,
    ]
])->toRawSql();
```

```sql
select * from "people" where "age" >= 18
```

#### LessThanFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$lt',
        'target' => 'age',
        'value'  => 18,
    ]
])->toRawSql();
```

```sql
select * from "people" where "age" < 18
```

#### LessThanEqualToFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$lte',
        'target' => 'age',
        'value'  => 18,
    ]
])->toRawSql();
```

```sql
select * from "people" where "age" <= 18
```

#### LikeFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$like',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" LIKE '%Laravel%'
```

#### LikeStartFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$like:start',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" LIKE 'Laravel%'
```

#### LikeEndFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$like:end',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" LIKE '%Laravel'
```

#### NotLikeFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$notLike',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" NOT LIKE '%Laravel%'
```

#### NotLikeStartFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$notLike:start',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" NOT LIKE 'Laravel%'
```

#### NotLikeEndFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$notLike:end',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" NOT LIKE '%Laravel'
```

#### OrFilter

- `value` = `array` of filters.

```php
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
            ]
        ]
    ]
])->toRawSql();
```

```sql
select * from "comments" where (("content" LIKE '%awesome%') or ("content" LIKE '%boring%'))
```

#### NullFilter

- `value` = `boolean` for `is null` or `is not null`.

```php
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
])->toRawSql();
```

```sql
select * from "people" where "age" is null and "weight" is not null
```

#### InFilter

- `value` = `array` of values.

```php
$sql = Person::filter([
    [
        'type'   => '$in',
        'target' => 'name',
        'value'  => ['Taylor', 'Otwell',]
    ],
])->toRawSql();
```

```sql
select * from "people" where "name" in ('Taylor', 'Otwell')
```

#### NotInFilter

- `value` = `array` of filters.

```php
$sql = Person::filter([
    [
        'type'   => '$notIn',
        'target' => 'name',
        'value'  => ['Nuno', 'Maduro',]
    ],
])->toRawSql();
```

```sql
select * from "people" where "name" not in ('Nuno', 'Maduro')
```

#### BetweenFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$between',
        'target' => 'age',
        'value'  => [18, 65,],
    ],
])->toRawSql();
```

```sql
select * from "people" where "age" between 18 and 65
```

#### NotBetweenFilter

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$notBetween',
        'target' => 'age',
        'value'  => [18, 65,],
    ],
])->toRawSql();
```

```sql
select * from "people" where "age" not between 18 and 65
```

#### HasFilter

- `value` = `array` of filters.

```php
$sql = Project::filter([
    [
        'type'   => '$has',
        'target' => 'comments',
        'value'  => [
            [
                'type'   => '$like',
                'target' => 'content',
                'value'  => 'awesome',
            ]
        ]
    ],
])->toRawSql();
```

```sql
select * from "projects" where exists (select * from "comments" where "projects"."id" = "comments"."project_id" and "content" LIKE '%awesome%')
```

#### DoesntHasFilter

- `value` = `array` of filters.

```php
$sql = Project::filter([
    [
        'type'   => '$doesntHas',
        'target' => 'comments',
        'value'  => [
            [
                'type'   => '$like',
                'target' => 'content',
                'value'  => 'boring',
            ]
        ]
    ],
])->toRawSql();
```

```sql
select * from "projects" where not exists (select * from "comments" where "projects"."id" = "comments"."project_id" and "content" LIKE '%boring%')
```

---

### Digging Deeper

#### Config

- Default configuration file

```php
return [
    'suppress' => [
        'filter' => [
            'denied'           => false,
            'missing'          => false,
            'invalid'          => false,
            'malformed_format' => false,
        ],
        'sort' => [
            'denied' => false,
        ],
    ],

    'custom_filters' => [

    ],
];
```

- The package throws various exception which can be suppressed.
- Custom filters should be registered in the config.

#### Custom Filters

---

### Error Handling

If you choose to not suppress exceptions in the config file and handle errors yourself:

All exceptions thrown by the package
implement `\IndexZer0\EloquentFiltering\Contracts\EloquentFilteringException`.

How-ever it doesn't harm to also catch `\Throwable`.

```php
try {
    Person::filter([])->get();
} catch (\IndexZer0\EloquentFiltering\Contracts\EloquentFilteringException $exception) {
    $exception->getMessage(); 
} catch (\Throwable $t) {
    // Shouldn't happen - but failsafe.
}
```

---

## Testing

```bash
composer test
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## Credits

- [IndexZer0](https://github.com/IndexZer0)
- [All Contributors](../../contributors)

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
