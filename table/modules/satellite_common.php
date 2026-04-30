<?php

require_once __DIR__ . '/../../includes/table_modules.php';

if (!function_exists('astro_satellite_request_string')) {
    function astro_satellite_request_string($request, $key, $default = '')
    {
        return isset($request[$key]) ? trim((string) $request[$key]) : $default;
    }
}

if (!function_exists('astro_satellite_request_int')) {
    function astro_satellite_request_int($request, $key, $default)
    {
        return isset($request[$key]) ? (int) $request[$key] : $default;
    }
}

if (!function_exists('astro_satellite_request_float')) {
    function astro_satellite_request_float($request, $key, $default)
    {
        return isset($request[$key]) ? (float) $request[$key] : $default;
    }
}

if (!function_exists('astro_satellite_counteralert_script')) {
    function astro_satellite_counteralert_script($alert_times, $alert_messages, $config_file, $max_alerts)
    {
        if (file_exists(astro_path($config_file))) {
            return '';
        }

        $now = time();
        $count = min(count($alert_times), $max_alerts);
        if ($count <= 0) {
            return '';
        }

        ob_start();
        echo '<script>';
        include astro_path('counteralert.js');
        echo "\n";
        for ($index = 0; $index < $count; $index++) {
            if ($now < $alert_times[$index]) {
                echo 'counteralert(',
                    ($alert_times[$index] - $now) * 1000,
                    ', ',
                    json_encode($alert_messages[$index]),
                    ');',
                    "\n";
            }
        }
        echo '</script>';
        return ob_get_clean();
    }
}
