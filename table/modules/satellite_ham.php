<?php

require_once __DIR__ . '/satellite_common.php';

return static function ($request) {
    $max = astro_satellite_request_int($request, 'max', 20);
    $ele = astro_satellite_request_int($request, 'ele', 15);
    $sat = astro_satellite_request_string($request, 'sat', '');

    $max_alert = 20;
    $satpri = array();
    $files = glob(astro_path('satellite/data/sat.*.table.tsv'));
    $filepri = astro_path('satellite/priority.list');
    $current = time();
    $alert_times = array();
    $alert_messages = array();
    $offset = 60;
    $headers = array();
    $rows = array();
    $sort_values = array();
    $tz_label = date('T');
    $priority_satellites = array();
    $other_satellites = array();

    $fh = fopen($filepri, 'r') or die("Cannot open file $filepri!\n");
    while (!feof($fh)) {
        $name = trim((string) fgets($fh));
        if ($name === '') {
            continue;
        }
        $satpri[$name] = 1;
        $fname = astro_path('satellite/data/sat.' . $name . '.table.tsv');
        if (file_exists($fname)) {
            $priority_satellites[] = $name;
        }
    }
    fclose($fh);

    foreach ($files as $file) {
        $parts = explode('.', basename($file));
        $name = $parts[1] ?? '';
        if ($name !== '' && !array_key_exists($name, $satpri)) {
            $other_satellites[] = $name;
        }
    }
    sort($other_satellites, SORT_NATURAL);

    $elevation_options = astro_numeric_options(array(5, 10, 15, 20, 25, 30, 40, 50, 60, 75, 90), $ele);
    $max_options = astro_numeric_options(array(10, 20, 30, 40, 50, 75, 100), $max);

    if ($sat !== '') {
        $fname = astro_path('satellite/data/sat.' . basename($sat) . '.table.tsv');
        if (file_exists($fname)) {
            $fh = fopen($fname, 'r') or die("Cannot open file $fname!\n");
            $count = 0;
            while (!feof($fh)) {
                $line = fgets($fh);
                if ($line === false) {
                    continue;
                }
                $fields = array_map('rtrim', explode("\t", $line));
                if (count($fields) !== 9) {
                    continue;
                }

                if (empty($headers)) {
                    $headers = $fields;
                    $headers[1] = str_replace('UTC', $tz_label, $headers[1]);
                    $headers[2] = str_replace('UTC', $tz_label, $headers[2]);
                    $headers[8] = str_replace('UTC', $tz_label, $headers[8]);
                    continue;
                }

                if ((int) $fields[5] < $ele) {
                    continue;
                }

                $start_time = strtotime($fields[1] . ' ' . $fields[2] . ' UTC');
                $end_time = strtotime($fields[1] . ' ' . $fields[8] . ' UTC');
                if ($end_time < $current) {
                    continue;
                }
                if ($count >= $max) {
                    break;
                }

                if ($start_time - $offset > $current) {
                    $alert_messages[] = $fields[0] . ' , Max Elevation: ' . $fields[5];
                    $alert_times[] = $start_time - $offset;
                }

                $display = $fields;
                $display[1] = date('D, n/j', $start_time);
                $display[2] = date('H:i:s', $start_time);
                $display[8] = date('H:i:s', $end_time);
                $rows[] = $display;
                $sort_values[] = array(
                    $display[0],
                    $start_time,
                    $start_time,
                    astro_duration_to_seconds($display[3]),
                    (int) $display[4],
                    (int) $display[5],
                    (int) $display[6],
                    (int) $display[7],
                    $end_time,
                );
                $count++;
            }
            fclose($fh);
        }
    }

    ob_start();
    echo '<form class="filter-form filter-form--compact" method="get" action="/table.php">';
    echo '<input type="hidden" name="tbm" value="satellite_ham">';
    echo '<div class="filter-field filter-field--inline">';
    echo '<label class="filter-field__label" for="satellite-name">Satellite</label>';
    echo '<select class="filter-select" id="satellite-name" name="sat" onchange="this.form.submit()">';
    echo astro_select_option('', 'Choose satellite', $sat === '');
    if (!empty($priority_satellites)) {
        echo '<optgroup label="Priority">';
        foreach ($priority_satellites as $name) {
            echo astro_select_option($name, $name, $sat === $name);
        }
        echo '</optgroup>';
    }
    if (!empty($other_satellites)) {
        echo '<optgroup label="All Satellites">';
        foreach ($other_satellites as $name) {
            echo astro_select_option($name, $name, $sat === $name);
        }
        echo '</optgroup>';
    }
    echo '</select>';
    echo '</div>';
    echo astro_inline_select_field('satellite-ele', 'ele', 'Min Elevation', array_combine($elevation_options, array_map(static function ($option) {
        return $option . ' deg';
    }, $elevation_options)), $ele);
    echo astro_inline_select_field('satellite-max', 'max', 'Rows', array_combine($max_options, $max_options), $max);
    echo '<div class="filter-actions"><a class="menu-state-link" href="/satellite/freq.php">Frequencies</a></div>';
    echo '<noscript><button class="filter-submit" type="submit">Apply</button></noscript>';
    echo '</form>';
    $controls_html = ob_get_clean();

    if (empty($headers)) {
        $sections = array(
            array(
                'type' => 'html',
                'html' => '<p class="page-note">Choose a satellite from the dropdown to view upcoming passes.</p>',
            ),
        );
    } else {
        $sections = array(
            array(
                'type' => 'sortable_table',
                'headers' => $headers,
                'rows' => $rows,
                'sort_values' => $sort_values,
                'options' => array('empty_message' => 'No upcoming passes match the current filters.'),
            ),
        );
    }

    return array(
        'title' => 'Satellite Ham',
        'controls_html' => $controls_html,
        'sections' => $sections,
        'scripts' => array(
            astro_satellite_counteralert_script($alert_times, $alert_messages, 'config/tgsathamalt.off', $max_alert),
        ),
    );
};
