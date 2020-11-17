<?php

if (!function_exists('__nova')) {
    function __nova($key, $replacements = [], $locale = null, $fallback = true)
    {
        $line = __($key, $replacements, $locale, $fallback);
        if (!isset($line) || $line === $key) $line = __($key, $replacements, app()->getFallbackLocale(), $fallback);
        return $line;
    }
}
