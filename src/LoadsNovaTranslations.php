<?php

namespace OptimistDigital\NovaTranslationsLoader;

use Laravel\Nova\Nova;
use Illuminate\Support\Str;
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

                // Load PHP translations
                $this->loadLaravelTranslations($pckgTransDir, $pckgName);

                // Attempt to load Nova translations
                if ($this->loadNovaTranslations($locale, 'project', $pckgTransDir, $pckgName)) return;
                if ($this->loadNovaTranslations($locale, 'local', $pckgTransDir, $pckgName)) return;
                if ($this->loadNovaTranslations($fallbackLocale, 'project', $pckgTransDir, $pckgName)) return;
                if ($this->loadNovaTranslations($fallbackLocale, 'local', $pckgTransDir, $pckgName)) return;
                $this->loadNovaTranslations('en', 'local', $pckgTransDir, $pckgName);
            });
        }
    }

    private function loadNovaTranslations($locale, $from, $packageTranslationsDir, $packageName)
    {
        $translationsFile = $this->getTranslationsFile($locale, $from, $packageTranslationsDir, $packageName);
        if ($translationsFile) {
            Nova::translations($translationsFile);
            return true;
        }
        return false;
    }

    private function loadLaravelTranslations($pckgTransDir, $pckgName)
    {
        $locale = app()->getLocale();
        $fbLocale = app()->getFallbackLocale();

        // Main locale
        $mainTransFile = $this->getTranslationsFile($locale, 'project', $pckgTransDir, $pckgName);
        if (!$mainTransFile) $mainTransFile = $this->getTranslationsFile($locale, 'local', $pckgTransDir, $pckgName);
        if ($mainTransFile) $this->loadTranslationsFromFileIntoTranslator($mainTransFile, $locale);


        // Fallback locale
        $fallbackTransFile = $this->getTranslationsFile($fbLocale, 'project', $pckgTransDir, $pckgName);
        if (!$fallbackTransFile) $fallbackTransFile = $this->getTranslationsFile($fbLocale, 'local', $pckgTransDir, $pckgName);
        if ($fallbackTransFile) $this->loadTranslationsFromFileIntoTranslator($fallbackTransFile, $fbLocale);

        if (!$fallbackTransFile && !$mainTransFile) {
            $enTransFile =  $this->getTranslationsFile('en', 'local', $pckgTransDir, $pckgName);
            if ($enTransFile) $this->loadTranslationsFromFileIntoTranslator($enTransFile, 'en');
        }
    }

    private function loadTranslationsFromFileIntoTranslator($filePath, $locale)
    {
        $fileContents = file_get_contents($filePath);
        $lines = collect(json_decode($fileContents, true))
            ->mapWithKeys(function ($value, $key) {
                return [Str::contains($key, '.') ? $key : "*.$key" => $value];
            })
            ->toArray();

        app('translator')->addLines($lines, $locale);
    }

    private function getTranslationsFile($locale, $from, $packageTranslationsDir, $packageName)
    {
        $fileDir = $from === 'local'
            ? $packageTranslationsDir
            : resource_path("lang/vendor/{$packageName}");

        $filePath = $locale ? "$fileDir/{$locale}.json" : $fileDir;

        $localeFileExists = File::exists($filePath);
        return $localeFileExists ? $filePath : null;
    }
}
