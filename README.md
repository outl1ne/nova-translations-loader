# Nova Translations Loader

[![Latest Version on Packagist](https://img.shields.io/packagist/v/optimistdigital/nova-translations-loader.svg?style=flat-square)](https://packagist.org/packages/optimistdigital/nova-translations-loader)
[![Total Downloads](https://img.shields.io/packagist/dt/optimistdigital/nova-translations-loader.svg?style=flat-square)](https://packagist.org/packages/optimistdigital/nova-translations-loader)

This [Laravel Nova](https://nova.laravel.com/) package helps developers load translations into their packages.

## Requirements

- `php: >=7.2`
- `laravel/framework: ^7.0 || ^8.0`
- `laravel/nova: ^3.0`

## Installation

Install the package in a Laravel Nova project via Composer:

```bash
composer require optimistdigital/nova-translations-loader
```

## Usage

Inside a Laravel's `ServiceProvider`, use the `LoadsNovaTranslations` trait and call `$this->loadTranslations()`:

```php
use OptimistDigital\NovaTranslationsLoader\LoadsNovaTranslations;

class SomePackagesServiceProvider extends ServiceProvider
{
    use LoadsNovaTranslations;

    public function boot()
    {
        // ...

        /**
         * Loads translations into the Nova system.
         *
         * @param string $packageTranslationsDir The directory for the packages' translation files.
         * @param string $packageName The name of the current package (ie 'nova-menu-builder').
         * @param boolean $publishTranslations Whether to also automatically make translations publishable.
         * @return null
         **/

        $this->loadTranslations(__DIR__ . '/../resources/lang', 'nova-package', true);

        // ...
    }
}

```

## Credits

- [Tarvo Reinpalu](https://github.com/Tarpsvo)

## License

Nova Translations Loader is open-sourced software licensed under the [MIT license](LICENSE.md).
