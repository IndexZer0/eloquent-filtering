# eloquent-filtering

[![Latest Version on Packagist](https://img.shields.io/packagist/v/indexzer0/eloquent-filtering.svg?style=flat-square)](https://packagist.org/packages/indexzer0/eloquent-filtering)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/indexzer0/eloquent-filtering/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/indexzer0/eloquent-filtering/actions?query=workflow%3Arun-tests+branch%3Amain)
[![codecov](https://codecov.io/gh/IndexZer0/eloquent-filtering/graph/badge.svg?token=34B3NIPBRM)](https://codecov.io/gh/IndexZer0/eloquent-filtering)
[![Total Downloads](https://img.shields.io/packagist/dt/indexzer0/eloquent-filtering.svg?style=flat-square)](https://packagist.org/packages/indexzer0/eloquent-filtering)

---

- **Avoid** writing custom query logic for filtering your models.

Features:

- Filter 
  - Many filter methods.
  - Filter by fields.
  - Filter by relationship existence.
    - Filter by fields on relationships.
      - No duplicated unnecessary exist clauses in queries.
  - Alias fields and relationships.
  - Specify filter types per field/relationship.
  - Json column filters.
  - Custom filters.
- Sort
  - Sort by fields.
  - Alias fields.
- Terminology based on laravel eloquent query builder.

---

## Simple example with relationship filter.

```php
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Filter\Filterable\SomeFiltersAllowed;

class Product extends Model
{
    use Filterable;
    
    protected function allowedFilters(): SomeFiltersAllowed
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
        'type'   => '$has',
        'target' => 'manufacturer',
        'value'  => [
            [
                'type'   => '$eq',
                'target' => 'name',
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
      - [Allowing All Filters](#allowing-all-filters)
    - [Filter Structure](#filter-structure)
    - [Available Filters](#available-filters)
      - [Field Filters](#field-filters)
      - [Relationship Filters](#relationship-filters)
      - [Condition Filters](#condition-filters)
      - [Json Field Filters](#json-field-filters)
    - [Digging Deeper](#digging-deeper)
        - [Config](#config)
        - [Default Allowed Filters](#default-allowed-filters)
        - [Aliasing Targets](#aliasing-targets)
        - [Specifying Allowed Types](#specifying-allowed-types)
        - [Suppressing Exceptions](#suppressing-exceptions)
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
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;

class Product extends Model
{
    use Filterable;
}
```

---

### Allowing Filters

By default, all filters are disallowed.

You can specify allowed filters in two ways:

#### Define on model.

```php
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
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

#### Allowing All Filters

You can allow all filters using `Filter::all()`.

See Caution in [Default Allowed Filters](#default-allowed-filters) section for security concerns when using this feature.

```php
protected function allowedFilters(): AllFiltersAllowed
{
    return Filter::all();
}
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

| Filter                                                                  | Code                 | Query                                                                 |
|-------------------------------------------------------------------------|----------------------|-----------------------------------------------------------------------|
| [EqualFilter](#EqualFilter---eq)                                        | `$eq`                | `{$target} = {$value}`                                                |
| [NotEqualFilter](#NotEqualFilter---noteq)                               | `$notEq`             | `{$target} != {$value}`                                               |
| [GreaterThanFilter](#GreaterThanFilter---gt)                            | `$gt`                | `{$target} > {$value}`                                                |
| [GreaterThanEqualToFilter](#GreaterThanEqualToFilter---gte)             | `$gte`               | `{$target} >= {$value}`                                               |
| [LessThanFilter](#LessThanFilter---lt)                                  | `$lt`                | `{$target} < {$value}`                                                |
| [LessThanEqualToFilter](#LessThanEqualToFilter---lte)                   | `$lte`               | `{$target} <= {$value}`                                               |
| [LikeFilter](#LikeFilter---like)                                        | `$like`              | `{$target} LIKE '%{$value}%'`                                         |
| [LikeStartFilter](#LikeStartFilter---likestart)                         | `$like:start`        | `{$target} LIKE '{$value}%'`                                          |
| [LikeEndFilter](#LikeEndFilter---likeend)                               | `$like:end`          | `{$target} LIKE '%{$value}'`                                          |
| [NotLikeFilter](#NotLikeFilter---notlike)                               | `$notLike`           | `{$target} NOT LIKE '%{$value}%'`                                     |
| [NotLikeStartFilter](#NotLikeStartFilter---notlikestart)                | `$notLike:start`     | `{$target} NOT LIKE '{$value}%'`                                      |
| [NotLikeEndFilter](#NotLikeEndFilter---notlikeend)                      | `$notLike:end`       | `{$target} NOT LIKE '%{$value}'`                                      |
| [NullFilter](#NullFilter---null)                                        | `$null`              | `{$target} is null` <code>&#124;&#124;</code> `{$target} is not null` |
| [InFilter](#InFilter---in)                                              | `$in`                | `{$target} in ($value)`                                               |
| [NotInFilter](#NotInFilter---notin)                                     | `$notIn`             | `{$target} not in ($value)`                                           |
| [BetweenFilter](#BetweenFilter---between)                               | `$between`           | `{$target} between $value[0] and $value[1]`                           |
| [NotBetweenFilter](#NotBetweenFilter---notbetween)                      | `$notBetween`        | `{$target} not between $value[0] and $value[1]`                       |
| [BetweenColumnsFilter](#BetweenColumnsFilter---betweencolumns)          | `$betweenColumns`    | `{$target} between $value[0] and $value[1]`                           |
| [NotBetweenColumnsFilter](#NotBetweenColumnsFilter---notbetweencolumns) | `$notBetweenColumns` | `{$target} not between $value[0] and $value[1]`                       |

#### Relationship Filters

| Filter                                          | Code             | Query                                                                 |
|-------------------------------------------------|------------------|-----------------------------------------------------------------------|
| [HasFilter](#HasFilter---has)                   | `$has`           | `where exists (select * from {$target})`                              |
| [DoesntHasFilter](#DoesntHasFilter---doesnthas) | `$doesntHas`     | `where not exists (select * from {$target})`                          |


#### Condition Filters

| Filter                        | Code   | Query |
|-------------------------------|--------|-------|
| [OrFilter](#OrFilter---or)    | `$or`  | `or`  |
| [AndFilter](#AndFilter---and) | `$and` | `and` |

#### Json Field Filters

| Filter                                                   | Code             | Query                                                                 |
|----------------------------------------------------------|------------------|-----------------------------------------------------------------------|
| [JsonContainsFilter](#JsonContainsFilter---jsoncontains) | `$jsonContains`  | `{$target} not between $value[0] and $value[1]`                       |

- Accepting pull requests for more common filters.

---

#### Filter Examples

#### EqualFilter - `$eq`

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

#### NotEqualFilter - `$notEq`

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

#### GreaterThanFilter - `$gt`

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

#### GreaterThanEqualToFilter - `$gte`

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

#### LessThanFilter - `$lt`

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

#### LessThanEqualToFilter - `$lte`

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

#### LikeFilter - `$like`

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

#### LikeStartFilter - `$like:start`

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

#### LikeEndFilter - `$like:end`

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

#### NotLikeFilter - `$notLike`

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

#### NotLikeStartFilter - `$notLike:start`

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

#### NotLikeEndFilter - `$notLike:end`

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

#### OrFilter - `$or`

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

#### AndFilter - `$and`

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

#### NullFilter - `$null`

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

#### InFilter - `$in`

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

#### NotInFilter - `$notIn`

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

#### BetweenFilter - `$between`

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

#### NotBetweenFilter - `$notBetween`

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

#### BetweenColumnsFilter - `$betweenColumns`

- `value` = `array` of strings.

```php
$sql = Product::filter([
    [
        'type'   => '$betweenColumns',
        'target' => 'price',
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

#### NotBetweenColumnsFilter - `$notBetweenColumns`

- `value` = `array` of strings.

```php
$sql = Product::filter([
    [
        'type'   => '$notBetweenColumns',
        'target' => 'price',
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

#### HasFilter - `$has`

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

#### DoesntHasFilter - `$doesntHas`

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
    'default_allowed_filter_list' => 'none',
    'default_allowed_sort_list'   => 'none',

    'suppress' => [
        'filter' => [
            'invalid'          => false,
            'missing'          => false,
            'malformed_format' => false,
            'denied'           => false,
        ],
        'sort' => [
            'malformed_format' => false,
            'denied'           => false,
        ],
    ],

    'custom_filters' => [

    ],
];
```

- The package throws various exception which can be suppressed.
- Custom filters should be registered in the config.

#### Default Allowed Filters

You can change the default allowed filters within the config file `eloquent-filtering`.

```php
'default_allowed_filter_list' => 'all',
```

> [!CAUTION]
> Allowing all filters by default and using filters from a HTTP request can put you at risk of sql injection due to PHP PDO can only bind values, not column names.

It is strongly suggested that you keep `default_allowed_filter_list` as `none` in your config and explicitly allow only specific filters with `Filter::only()`.

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

You can also alias targets when allowing all filters.

```php
Filter::all(
    Target::alias('name', 'first_name'),
    Target::relationAlias(
        'documents',
        'files',
        Target::alias('file_extension', 'mime_type')
    ),
)
```

#### Specifying Allowed Types

```php
use IndexZer0\EloquentFiltering\Filter\Types\Types;

// Only `$eq` allowed
Filter::field('name', ['$eq'])
Filter::field('name', Types::only(['$eq']))

// All types allowed
Filter::field('name', Types::all()),

// All except `$eq` allowed
Filter::field('name', Types::except(['$eq'])),
```

#### Suppressing Exceptions

Various exceptions are thrown by this package. Most can be suppressed globally in the config file.

When suppressing an exception, filters that caused the exception will be ignored.

- Suppressible

```php
class InvalidFilterException
config("eloquent-filtering.suppress.filter.invalid");
// Filter does not have `type` key.

class MissingFilterException
config("eloquent-filtering.suppress.filter.missing");
// Can't find filter of `type` specified.

class MalformedFilterFormatException
config("eloquent-filtering.suppress.filter.malformed_format");
// The filter was found, but the rest of the data does not match required format of the filter.

class DeniedFilterException
config("eloquent-filtering.suppress.filter.denied");
// Filter is not allowed.
``` 

- Not Suppressible

```php
class DuplicateFiltersException
// When you have registered a custom filter that has the same type as another filter.
``` 

#### Custom Filters

---

### Error Handling

All exceptions thrown by the package implement `\IndexZer0\EloquentFiltering\Contracts\EloquentFilteringException`.

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
