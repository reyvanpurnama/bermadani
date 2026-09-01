<?php

use App\Models\CooperativeSetting;

if (!function_exists('coop_setting')) {
    /**
     * Get a cooperative setting from database (cached).
     *
     * @param string $key   The setting key
     * @param mixed $default Default value if not found
     * @return string|null
     */
    function coop_setting(string $key, $default = null): ?string
    {
        return CooperativeSetting::getValue($key, $default);
    }
}

if (!function_exists('coop_config')) {
    /**
     * Shortcut for config('cooperative.*') with dot notation.
     *
     * Usage: coop_config('name') → config('cooperative.name')
     *        coop_config('theme.primary') → config('cooperative.theme.primary')
     *
     * @param string $key   Dot-notation key relative to 'cooperative.'
     * @param mixed $default Default value
     * @return mixed
     */
    function coop_config(string $key, $default = null)
    {
        return config("cooperative.{$key}", $default);
    }
}
