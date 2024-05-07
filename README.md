# eloquent-filtering

[![Latest Version on Packagist](https://img.shields.io/packagist/v/indexzer0/eloquent-filtering.svg?style=flat-square)](https://packagist.org/packages/indexzer0/eloquent-filtering)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/indexzer0/eloquent-filtering/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/indexzer0/eloquent-filtering/actions?query=workflow%3Arun-tests+branch%3Amain)
[![codecov](https://codecov.io/gh/IndexZer0/eloquent-filtering/graph/badge.svg?token=34B3NIPBRM)](https://codecov.io/gh/IndexZer0/eloquent-filtering)
[![Total Downloads](https://img.shields.io/packagist/dt/indexzer0/eloquent-filtering.svg?style=flat-square)](https://packagist.org/packages/indexzer0/eloquent-filtering)

---

- **Avoid** writing custom query logic for filtering your models.

---

## Simple example with relationship filter.

```php
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable

class Product extends Model
{
    use Filterable;
    
    protected function allowedFilters(): FilterableList
    {
        return Filter::only(
            Filter::field('name', ['$eq']),
            Filter::relation('manufacturer', ['$has'],
                Filter::only(
                    Filter::field('name', ['$eq'])
                )
            )
        );
    }
    
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
    - [Filter Structure](#filter-structure)
    - [Available Filters](#available-filters)
      - [Field Filters](#field-filters)
      - [Relationship Filters](#relationship-filters)
      - [Condition Filters](#condition-filters)
      - [Json Field Filters](#json-field-filters)
    - [Digging Deeper](#digging-deeper)
        - [Config](#config)
        - [Aliasing Targets](#aliasing-targets)
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

By default, all filters are disallowed.

You can change the default within the config file `eloquent-filtering`.

```php
'default_allowed_filter_list' => 'all',
```

> [!CAUTION]
> Allowing all filters by default and using filters from a HTTP request can put you at risk of sql injection due to PHP PDO can only bind values, not column names.

It is strongly suggested that you keep `default_allowed_filter_list` to `none` in your config and explicitly allow only specific filters.

You can specify specific filters in two ways:

#### Define on model.

```php
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable
use IndexZer0\EloquentFiltering\Filter\Filterable\SomeFiltersAllowed;

class Product extends Model
{
    use Filterable;
    
    protected function allowedFilters(): SomeFiltersAllowed
    {
        return Filter::only(
            Filter::field('name', ['$eq', '$like']),
            Filter::relation(
                'manufacturer', 
                ['$has', '$doesntHas'],
                Filter::only(
                    Filter::field('name', ['$like'])
                )
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
    Filter::only(
        Filter::field('name', ['$eq']),
        Filter::relation(
            'manufacturer', 
            ['$has', '$doesntHas'],
            Filter::only(
                Filter::field('name', ['$like'])
            )
        )
    )
)->get();
```

---

### Filter Structure

- Filters **ALWAYS** have a `type`.
- All filters apart from `$or` and `$and` have a `target`.
- Filter `value` is different depending on the filter.

---

### Available Filters

This package provides core filters that give you the ability to perform the vast majority of the filtering you'd need.

#### Field Filters

| Filter                                                | Code                 | Query                                                                 |
|-------------------------------------------------------|----------------------|-----------------------------------------------------------------------|
| [EqualFilter](#EqualFilter)                           | `$eq`                | `{$target} = {$value}`                                                |
| [NotEqualFilter](#NotEqualFilter)                     | `$notEq`             | `{$target} != {$value}`                                               |
| [GreaterThanFilter](#GreaterThanFilter)               | `$gt`                | `{$target} > {$value}`                                                |
| [GreaterThanEqualToFilter](#GreaterThanEqualToFilter) | `$gte`               | `{$target} >= {$value}`                                               |
| [LessThanFilter](#LessThanFilter)                     | `$lt`                | `{$target} < {$value}`                                                |
| [LessThanEqualToFilter](#LessThanEqualToFilter)       | `$lte`               | `{$target} <= {$value}`                                               |
| [LikeFilter](#LikeFilter)                             | `$like`              | `{$target} LIKE '%{$value}%'`                                         |
| [LikeStartFilter](#LikeStartFilter)                   | `$like:start`        | `{$target} LIKE '{$value}%'`                                          |
| [LikeEndFilter](#LikeEndFilter)                       | `$like:end`          | `{$target} LIKE '%{$value}'`                                          |
| [NotLikeFilter](#NotLikeFilter)                       | `$notLike`           | `{$target} NOT LIKE '%{$value}%'`                                     |
| [NotLikeStartFilter](#NotLikeStartFilter)             | `$notLike:start`     | `{$target} NOT LIKE '{$value}%'`                                      |
| [NotLikeEndFilter](#NotLikeEndFilter)                 | `$notLike:end`       | `{$target} NOT LIKE '%{$value}'`                                      |
| [NullFilter](#NullFilter)                             | `$null`              | `{$target} is null` <code>&#124;&#124;</code> `{$target} is not null` |
| [InFilter](#InFilter)                                 | `$in`                | `{$target} in ($value)`                                               |
| [NotInFilter](#NotInFilter)                           | `$notIn`             | `{$target} not in ($value)`                                           |
| [BetweenFilter](#BetweenFilter)                       | `$between`           | `{$target} between $value[0] and $value[1]`                           |
| [NotBetweenFilter](#NotBetweenFilter)                 | `$notBetween`        | `{$target} not between $value[0] and $value[1]`                       |
| [BetweenColumnsFilter](#BetweenColumnsFilter)         | `$betweenColumns`    | `{$target} between $value[0] and $value[1]`                           |
| [NotBetweenColumnsFilter](#NotBetweenColumnsFilter)   | `$notBetweenColumns` | `{$target} not between $value[0] and $value[1]`                       |

#### Relationship Filters

| Filter                                                | Code             | Query                                                                 |
|-------------------------------------------------------|------------------|-----------------------------------------------------------------------|
| [HasFilter](#HasFilter)                               | `$has`           | `where exists (select * from {$target})`                              |
| [DoesntHasFilter](#DoesntHasFilter)                   | `$doesntHas`     | `where not exists (select * from {$target})`                          |


#### Condition Filters

| Filter                  | Code   | Query |
|-------------------------|--------|-------|
| [OrFilter](#OrFilter)   | `$or`  | `or`  |
| [AndFilter](#AndFilter) | `$and` | `and` |

#### Json Field Filters

| Filter                                                | Code             | Query                                                                 |
|-------------------------------------------------------|------------------|-----------------------------------------------------------------------|
| [JsonContainsFilter](#JsonContainsFilter)             | `$jsonContains`  | `{$target} not between $value[0] and $value[1]`                       |

- Accepting pull requests for more common filters.

---

#### Filter Examples

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
select * from "people" where "age" > 18
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

#### AndFilter

- `value` = `array` of filters.

```php
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
            ]
        ]
    ]
])->toRawSql();
```

```sql
select * from "comments" where (("content" LIKE '%is awesome%') and ("content" LIKE '%is not boring%'))
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

#### BetweenColumnsFilter

- `value` = `array` of strings.

```php
$sql = Product::filter([
    [
        'target' => 'price',
        'type'   => '$betweenColumns',
        'value'  => [
            'min_allowed_price',
            'max_allowed_price',
        ],
    ],
])->toRawSql();
```

```sql
select * from "products" where "price" between "min_allowed_price" and "max_allowed_price"
```

#### NotBetweenColumnsFilter

- `value` = `array` of strings.

```php
$sql = Product::filter([
    [
        'target' => 'price',
        'type'   => '$notBetweenColumns',
        'value'  => [
            'min_allowed_price',
            'max_allowed_price',
        ],
    ],
])->toRawSql();
```

```sql
select * from "products" where "price" between "min_allowed_price" and "max_allowed_price"
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

#### Aliasing Targets

You can alias your target fields and relations if you don't wish to expose database field names to your frontend.

The below example:
 - Allows `name` and uses `first_name` in the database query.
 - Allows `documents` and uses `files` as the relationship name.

```php
$sql = Person::filter([
    [
        'type'   => '$eq',
        'target' => 'name',
        'value'  => 'Taylor',
    ],
    [
        'type'   => '$has',
        'target' => 'documents',
        'value'  => [],
    ],
], Filter::only(
    Filter::field(Target::alias('name', 'first_name'), ['$eq']),
    Filter::relation(Target::alias('documents', 'files'), ['$has'])
))->toRawSql();
```

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
