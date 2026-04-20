<?php

require_once __DIR__ . '/includes/site.php';

header('Content-Type: application/json; charset=UTF-8');

try {
    $skycover_file = weather_plot_resolve_input_file($_GET['f1'] ?? null, true);
    $humidity_file = weather_plot_resolve_input_file($_GET['f2'] ?? null, false);
    $temperature_file = weather_plot_resolve_input_file($_GET['f3'] ?? null, false);

    $sites = astro_load_site_data();
    $skycover_rows = weather_plot_load_rows($skycover_file, 'sky_cover');
    $humidity_rows = weather_plot_load_rows($humidity_file, 'humidity');
    $temperature_rows = weather_plot_load_rows($temperature_file, 'temperature');

    $payload = array(
        'generated_at' => gmdate('Y-m-d\TH:i:s\Z'),
        'layout' => array(
            'width' => 873,
            'height' => 164,
            'temp_min_c' => -10,
            'temp_max_c' => 40,
        ),
        'sites' => array(),
    );

    foreach ($sites as $site) {
        $site_name = (string) ($site['name'] ?? '');
        $site_payload = weather_plot_build_site_payload(
            $site,
            $skycover_rows[$site_name] ?? array(),
            $humidity_rows[$site_name] ?? array(),
            $temperature_rows[$site_name] ?? array()
        );
        if ($site_payload !== null) {
            $payload['sites'][] = $site_payload;
        }
    }

    echo json_encode($payload, JSON_UNESCAPED_SLASHES), "\n";
} catch (Throwable $exception) {
    http_response_code(400);
    echo json_encode(array('error' => $exception->getMessage()), JSON_UNESCAPED_SLASHES), "\n";
}

function weather_plot_resolve_input_file($value, $required)
{
    $value = trim((string) $value);
    if ($value === '') {
        if ($required) {
            throw new InvalidArgumentException('Missing required weather plot file.');
        }
        return null;
    }

    $resolved = realpath($value);
    if ($resolved === false || !is_file($resolved)) {
        throw new InvalidArgumentException('Weather plot file does not exist: ' . $value);
    }

    $astro_root = realpath(astro_path());
    if ($astro_root === false || strpos($resolved, $astro_root . DIRECTORY_SEPARATOR) !== 0) {
        throw new InvalidArgumentException('Weather plot file is outside the astro repository.');
    }

    return $resolved;
}

function weather_plot_load_rows($path, $metric)
{
    if ($path === null) {
        return array();
    }

    $timezone = weather_plot_detect_timezone($path);
    $handle = fopen($path, 'r');
    if (!$handle) {
        throw new RuntimeException('Cannot open weather plot file: ' . $path);
    }

    $rows = array();
    while (($line = fgets($handle)) !== false) {
        $parts = explode("\t", trim($line));
        if (count($parts) !== 3) {
            continue;
        }

        $site_name = trim($parts[0]);
        $timestamp = strtotime($parts[1] . ' ' . $timezone);
        if ($site_name === '' || $timestamp === false) {
            continue;
        }

        $rows[$site_name][] = array(
            'timestamp' => (int) $timestamp,
            'label' => date('D, n/j H:i', $timestamp),
            'value' => weather_plot_normalize_metric($metric, $parts[2]),
            'raw_value' => is_numeric($parts[2]) ? (float) $parts[2] : null,
        );
    }

    fclose($handle);
    return $rows;
}

function weather_plot_detect_timezone($path)
{
    if (preg_match('/([A-Z]{3})\.format$/', basename((string) $path), $matches)) {
        return $matches[1];
    }
    return 'UTC';
}

function weather_plot_normalize_metric($metric, $value)
{
    if (!is_numeric($value)) {
        return null;
    }

    $numeric = (float) $value;
    if ($metric === 'temperature') {
        return round($numeric - 273.15, 1);
    }

    return round($numeric, 1);
}

function weather_plot_build_site_payload($site, $skycover_rows, $humidity_rows, $temperature_rows)
{
    if ($skycover_rows === array() && $humidity_rows === array() && $temperature_rows === array()) {
        return null;
    }

    $time_map = array();
    foreach (array($skycover_rows, $humidity_rows, $temperature_rows) as $rows) {
        foreach ($rows as $row) {
            $time_map[$row['timestamp']] = $row['label'];
        }
    }
    ksort($time_map);
    $timestamps = array_keys($time_map);
    if ($timestamps === array()) {
        return null;
    }

    $base_time = (int) $timestamps[0];
    $x = array();
    $x_labels = array();
    foreach ($timestamps as $timestamp) {
        $x[] = round(($timestamp - $base_time) / 3600, 3);
        $x_labels[] = $time_map[$timestamp];
    }

    return array(
        'name' => (string) ($site['name'] ?? ''),
        'latitude' => (float) ($site['latitude'] ?? 0.0),
        'longitude' => (float) ($site['longitude'] ?? 0.0),
        'clear_dark_sky_link' => (string) ($site['clear_dark_sky_link'] ?? ''),
        'x' => $x,
        'x_labels' => $x_labels,
        'series' => array(
            'sky_cover' => weather_plot_align_series($timestamps, $skycover_rows),
            'humidity' => weather_plot_align_series($timestamps, $humidity_rows),
            'temperature_c' => weather_plot_align_series($timestamps, $temperature_rows),
            'temperature_plot' => weather_plot_align_series($timestamps, $temperature_rows, true),
        ),
    );
}

function weather_plot_align_series($timestamps, $rows, $scale_temperature = false)
{
    $by_time = array();
    foreach ($rows as $row) {
        $value = $row['value'];
        if ($scale_temperature && $value !== null) {
            $value = weather_plot_scale_temperature($value);
        }
        $by_time[$row['timestamp']] = $value;
    }

    $aligned = array();
    foreach ($timestamps as $timestamp) {
        $aligned[] = array_key_exists($timestamp, $by_time) ? $by_time[$timestamp] : null;
    }

    return $aligned;
}

function weather_plot_scale_temperature($temp_c)
{
    $temp_min = -10.0;
    $temp_max = 40.0;
    return round((($temp_c - $temp_min) / ($temp_max - $temp_min)) * 100.0, 3);
}
