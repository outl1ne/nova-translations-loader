<?php

namespace OptimistDigital\NovaTranslationsLoader;

use Exception;
use Laravel\Nova\Nova;
use Illuminate\Support\Arr;
use Illuminate\Container\Container;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Contracts\Translation\Loader;

trait LoadsNovaTranslations
{
    protected $packageTranslationsDir;
    protected $packageName;
    protected $publishTranslations;

    /**
     * Loads translations into the Nova system.
     *
     * @param string $packageTranslationsDir The directory for the packages' translation files.
     * @param string $packageName The name of the current package (ie 'nova-menu-builder').
     * @param boolean $publishTranslations Whether to also automatically make translations publishable.
     * @return null
     **/
    protected function loadTranslations($packageTranslationsDir, $packageName, $publishTranslations = true)
    {
        $packageTranslationsDir = $packageTranslationsDir ?? __DIR__ . '/../resources/lang';
        $packageTranslationsDir = rtrim($packageTranslationsDir, '/');
        $packageName = trim($packageName);
        $this->translations($packageTranslationsDir, $packageName, $publishTranslations);
    }

    private function translations($pckgTransDir, $pckgName, $publish)
    {
        if (app()->runningInConsole() && $publish) {
            $this->publishes([$pckgTransDir => resource_path("lang/vendor/{$pckgName}")], 'translations');
            return;
        }

        $this->loadTranslationsFrom($pckgTransDir, 'nova-menu-builder');

        if (!method_exists(Nova::class, 'translations')) throw new Exception('Nova::translations method not found, please ensure you are using the correct version of Nova.');

        Nova::serving(function (ServingNova $event) use ($pckgTransDir, $pckgName) {
            /** @var Loader $loader */
            $loader = Container::getInstance()->make('translation.loader');

            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale');

            Nova::translations(array_merge(
                Arr::dot($loader->load('en', 'nova', $pckgName), "{$pckgName}::"),
                Arr::dot($loader->load($fallbackLocale, 'nova', $pckgName), "{$pckgName}::"),
                Arr::dot($loader->load($locale, 'nova', $pckgName), "{$pckgName}::")
            ));
        });
    }
}
