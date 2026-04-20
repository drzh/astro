<?php

header('Content-Type: application/json; charset=UTF-8');

try {
    $path = __DIR__ . '/economy/treasury/treasury_rate.300d.data';
    if (!is_file($path)) {
        throw new RuntimeException('Treasury data file not found.');
    }

    $series_order = array('3Mo', '1Yr', '2Yr', '5Yr', '10Yr', '30Yr');
    $labels = array();
    $series_map = array();
    foreach ($series_order as $name) {
        $series_map[$name] = array();
    }

    $handle = fopen($path, 'r');
    if (!$handle) {
        throw new RuntimeException('Unable to open treasury data file.');
    }

    while (($line = fgets($handle)) !== false) {
        $parts = explode("\t", trim($line));
        if (count($parts) < 4) {
            continue;
        }

        $x = (int) $parts[0];
        $label = economy_plot_format_date(trim($parts[1]));
        $value = is_numeric($parts[2]) ? (float) $parts[2] : null;
        $series_name = trim($parts[3]);
        if (!array_key_exists($series_name, $series_map)) {
            continue;
        }

        $labels[$x] = $label;
        $series_map[$series_name][$x] = $value;
    }
    fclose($handle);

    if ($labels === array()) {
        throw new RuntimeException('No treasury data available.');
    }

    ksort($labels);
    $x_values = array_keys($labels);
    $x_labels = array_values($labels);

    $y_values = array();
    foreach ($series_order as $name) {
        $aligned = array();
        foreach ($x_values as $x) {
            $aligned[] = array_key_exists($x, $series_map[$name]) ? $series_map[$name][$x] : null;
        }
        $y_values[$name] = $aligned;
    }

    $all_numeric = array();
    foreach ($y_values as $aligned) {
        foreach ($aligned as $value) {
            if ($value !== null) {
                $all_numeric[] = $value;
            }
        }
    }
    if ($all_numeric === array()) {
        throw new RuntimeException('Treasury series contain no numeric values.');
    }

    $min_value = min($all_numeric);
    $max_value = max($all_numeric);
    $diff = $max_value - $min_value;
    $mag = 1e-2;
    while ($diff / $mag > 2) {
        $mag *= 20;
    }
    $mag /= 20;

    $payload = array(
        'generated_at' => gmdate('Y-m-d\TH:i:s\Z'),
        'layout' => array(
            'width' => 873,
            'height' => 290,
            'y_min' => floor($min_value / $mag) * $mag,
            'y_max' => ceil($max_value / $mag) * $mag,
        ),
        'chart' => array(
            'title' => 'Treasury Rate',
            'meta' => 'Historical chart',
            'x' => $x_values,
            'x_labels' => $x_labels,
            'series' => $y_values,
        ),
    );

    echo json_encode($payload, JSON_UNESCAPED_SLASHES), "\n";
} catch (Throwable $exception) {
    http_response_code(400);
    echo json_encode(array('error' => $exception->getMessage()), JSON_UNESCAPED_SLASHES), "\n";
}

function economy_plot_format_date($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    try {
        return (new DateTimeImmutable($value))->format('Y-m-d');
    } catch (Throwable $exception) {
        return $value;
    }
}
