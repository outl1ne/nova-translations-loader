# Nova Translations Loader

[![Latest Version on Packagist](https://img.shields.io/packagist/v/outl1ne/nova-translations-loader.svg?style=flat-square)](https://packagist.org/packages/outl1ne/nova-translations-loader)
[![Total Downloads](https://img.shields.io/packagist/dt/outl1ne/nova-translations-loader.svg?style=flat-square)](https://packagist.org/packages/outl1ne/nova-translations-loader)

This [Laravel Nova](https://nova.laravel.com/) package helps developers load translations into their packages.

## Requirements

- `php: >=8.0`
- `laravel/framework: ^9.0|^10.0`
- `laravel/nova: ^4.0`

## Installation

Install the package in a Laravel Nova project via Composer:

```bash
composer require outl1ne/nova-translations-loader
```

## Usage

Inside a Laravel's `ServiceProvider`, use the `LoadsNovaTranslations` trait and call `$this->loadTranslations()`:

```php
use Outl1ne\NovaTranslationsLoader\LoadsNovaTranslations;

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
