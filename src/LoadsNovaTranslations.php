<?php

namespace OptimistDigital\NovaTranslationsLoader;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\File;

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

        if (method_exists('Nova', 'translations')) {
            Nova::serving(function (ServingNova $event) use ($pckgTransDir, $pckgName) {
                $locale = app()->getLocale();
                $fallbackLocale = config('app.fallback_locale');

                if ($this->attemptToLoadTranslations($locale, 'project', $pckgTransDir, $pckgName)) return;
                if ($this->attemptToLoadTranslations($locale, 'local', $pckgTransDir, $pckgName)) return;
                if ($this->attemptToLoadTranslations($fallbackLocale, 'project', $pckgTransDir, $pckgName)) return;
                if ($this->attemptToLoadTranslations($fallbackLocale, 'local', $pckgTransDir, $pckgName)) return;
                $this->attemptToLoadTranslations('en', 'local', $pckgTransDir, $pckgName);
            });
        }
    }

    private function attemptToLoadTranslations($locale, $from, $packageTranslationsDir, $packageName)
    {
        $fileDir = $from === 'local'
            ? $packageTranslationsDir
            : resource_path("lang/vendor/{$packageName}");

        $filePath = "$fileDir/{$locale}.json";

        $localeFileExists = File::exists($filePath);
        if ($localeFileExists) {
            $this->loadJsonTranslationsFrom($fileDir);
            Nova::translations($filePath);
            return true;
        }
        return false;
    }
}
