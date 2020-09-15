<?php

namespace OptimistDigital\NovaTranslationsLoader;

use Laravel\Nova\Nova;
use Illuminate\Support\ServiceProvider;

class NovaTranslationsLoader extends ServiceProvider
{
    protected $packageDir;
    protected $packageName;
    protected $publishTranslations;

    public function __construct($packageDir = __DIR__, $packageName, $publishTranslations = true)
    {
        $this->packageDir = $packageDir;
        $this->packageName = $packageName;
        $this->publishTranslations = $publishTranslations;
    }

    public static function loadTranslations($packageDir = __DIR__, $packageName, $publishTranslations = true)
    {
        $translationsLoader = new NovaTranslationsLoader($packageDir, $packageName, $publishTranslations);
        return $translationsLoader->translations();
    }

    protected function translations()
    {
        if (app()->runningInConsole() && $this->publishTranslations) {
            $this->publishes(["{$this->packageDir}/../resources/lang" => resource_path("lang/vendor/{$this->packageName}")], 'translations');
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
            ? "{$this->packageDir}/../resources/lang/{$locale}.json"
            : resource_path("lang/vendor/{$this->packageName}/{$locale}.json");

        $localeFileExists = File::exists($filePath);
        if ($localeFileExists) {
            Nova::translations($filePath);
            return true;
        }
        return false;
    }
}
