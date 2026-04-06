<?php

require_once __DIR__ . '/bootstrap.php';

if (!function_exists('astro_site_data_path')) {
    function astro_site_data_path()
    {
        return astro_path('site/site.json');
    }
}

if (!function_exists('astro_load_site_data')) {
    function astro_load_site_data($path = null)
    {
        $path = $path ?: astro_site_data_path();
        if (!file_exists($path)) {
            throw new RuntimeException('Cannot find site data file: ' . $path);
        }

        $json = file_get_contents($path);
        if ($json === false) {
            throw new RuntimeException('Cannot read site data file: ' . $path);
        }

        $sites = json_decode($json, true);
        if (!is_array($sites)) {
            throw new RuntimeException('Invalid site data JSON in: ' . $path);
        }

        $normalized = array();
        foreach ($sites as $index => $site) {
            if (!is_array($site)) {
                throw new RuntimeException('Invalid site record at index ' . $index . ' in: ' . $path);
            }

            $normalized[] = array(
                'name' => (string) ($site['name'] ?? ''),
                'latitude' => (float) ($site['latitude'] ?? 0),
                'longitude' => (float) ($site['longitude'] ?? 0),
                'clear_dark_sky_image' => (string) ($site['clear_dark_sky_image'] ?? ''),
                'clear_dark_sky_link' => (string) ($site['clear_dark_sky_link'] ?? ''),
                'slug' => (string) ($site['slug'] ?? ''),
                'meteogram_url' => (string) ($site['meteogram_url'] ?? ''),
                'state' => (string) ($site['state'] ?? ''),
            );
        }

        return $normalized;
    }
}
