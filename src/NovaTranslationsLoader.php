<?php

namespace OptimistDigital\NovaTranslationsLoader;

use Laravel\Nova\Nova;
use Illuminate\Support\ServiceProvider;

class NovaTranslationsLoader extends ServiceProvider
{
    protected $packageTranslationsDir;
    protected $packageName;
    protected $publishTranslations;

    public function __construct($packageTranslationsDir = __DIR__, $packageName, $publishTranslations = true)
    {
        $this->packageTranslationsDir = $packageTranslationsDir;
        $this->packageName = $packageName;
        $this->publishTranslations = $publishTranslations;
    }

    /**
     * Loads translations into the Nova system.
     *
     * @param string $packageTranslationsDir The directory for the packages' translation files.
     * @param string $packageName The name of the current package (ie 'nova-menu-builder').
     * @param boolean $publishTranslations Whether to also automatically make translations publishable.
     * @return null
     **/
    public static function loadTranslations($packageTranslationsDir = __DIR__, $packageName, $publishTranslations = true)
    {
        $packageTranslationsDir = rtrim($packageTranslationsDir, '/');
        $packageName = trim($packageName);

        $translationsLoader = new NovaTranslationsLoader($packageTranslationsDir, $packageName, $publishTranslations);
        return $translationsLoader->translations();
    }

    protected function translations()
    {
        if (app()->runningInConsole() && $this->publishTranslations) {
            $this->publishes([$this->packageTranslationsDir => resource_path("lang/vendor/{$this->packageName}")], 'translations');
            return;
        }

        if (method_exists('Nova', 'translations')) {
            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale');

            if ($this->attemptToLoadTranslations($locale, 'project')) return;
            if ($this->attemptToLoadTranslations($locale, 'local')) return;
            if ($this->attemptToLoadTranslations($fallbackLocale, 'project')) return;
            if ($this->attemptToLoadTranslations($fallbackLocale, 'local')) return;
            $this->attemptToLoadTranslations('en', 'local');
        }
    }

    protected function attemptToLoadTranslations($locale, $from)
    {
        $filePath = $from === 'local'
            ? "{$this->packageTranslationsDir}/{$locale}.json"
            : resource_path("lang/vendor/{$this->packageName}/{$locale}.json");

        $localeFileExists = File::exists($filePath);
        if ($localeFileExists) {
            Nova::translations($filePath);
            return true;
        }
        return false;
    }
}
