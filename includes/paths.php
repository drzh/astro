<?php

if (!defined('ASTRO_ROOT')) {
    define('ASTRO_ROOT', dirname(__DIR__));
}

if (!defined('ASTRO_INCLUDES')) {
    define('ASTRO_INCLUDES', ASTRO_ROOT . '/includes');
}

if (!function_exists('astro_path')) {
    function astro_path($relative = '')
    {
        $relative = ltrim((string) $relative, '/');
        return $relative === '' ? ASTRO_ROOT : ASTRO_ROOT . '/' . $relative;
    }
}

if (!function_exists('astro_include_path')) {
    function astro_include_path($relative = '')
    {
        $relative = ltrim((string) $relative, '/');
        return $relative === '' ? ASTRO_INCLUDES : ASTRO_INCLUDES . '/' . $relative;
    }
}
