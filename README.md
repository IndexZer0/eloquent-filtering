<h1 align="center">Eloquent Filtering</h3>

![Filter example](/docs/images/hero.png)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/indexzer0/eloquent-filtering.svg?style=for-the-badge)](https://packagist.org/packages/indexzer0/eloquent-filtering)
[![Total Downloads](https://img.shields.io/packagist/dt/indexzer0/eloquent-filtering.svg?style=for-the-badge&color=007ec6)](https://packagist.org/packages/indexzer0/eloquent-filtering)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/indexzer0/eloquent-filtering/run-tests.yml?branch=main&label=tests&style=for-the-badge)](https://github.com/indexzer0/eloquent-filtering/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Codecov](https://img.shields.io/codecov/c/github/IndexZer0/eloquent-filtering?token=34B3NIPBRM&style=for-the-badge&logo=codecov)](https://codecov.io/gh/IndexZer0/eloquent-filtering)

---

<h2 align="center">Easily filter eloquent models using arrays</h2>

*Eloquent Filtering* **simplifies** implementing search functionality for your Eloquent models, whether simple or complex, by **eliminating** the need for custom query logic.
It allows you to **easily** define and manage filters directly within your models, and **seamlessly** apply them using incoming HTTP request data to dynamically filter your models.

With this package, you can build more readable, maintainable, and scalable code, **boosting** your productivity and **speeding up** development.

Whether you’re building APIs, dashboards, or advanced search systems, *Eloquent Filtering* provides a **powerful** and **flexible** way to streamline your Eloquent queries, making it easier to manage and extend your application’s filtering capabilities.

---

# [View The Docs](https://docs.eloquentfiltering.com)

---

## Quick Look

```php
class Product extends Model implements IsFilterable
{
    use Filterable;

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('name', [FilterType::EQUAL]),
        );
    }
}

$products = Product::filter([
    [
        'target' => 'name',
        'type'   => '$eq',
        'value'  => 'TV'
    ]
])->get();
```

---

## Requirements

- PHP Version >= `8.2`
- Laravel Version >= `10`

---

## Installation

You can install the package via composer:

```bash
composer require indexzer0/eloquent-filtering
```

Run the `install` artisan command to publish the config:

```bash
php artisan eloquent-filtering:install
```

---

## Testing

```bash
composer test
```

---

## Docs

```bash
npm i -g mintlify
cd docs
mintlify dev
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## Feature Ideas

Please see [Feature ideas](feature-ideas.md) for potential future features.

---

## Credits

- [IndexZer0](https://github.com/IndexZer0)
- [All Contributors](../../contributors)

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
