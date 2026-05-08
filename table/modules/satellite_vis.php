<?php

require_once __DIR__ . '/satellite_common.php';

return static function ($request) {
    $max = astro_satellite_request_int($request, 'max', 20);
    $sat = astro_satellite_request_string($request, 'sat', '');
    $mag = astro_satellite_request_float($request, 'mag', 3.0);

    $max_alert = 20;
    $files = glob(astro_path('satellite/ha/*.tsv'));
    $current = time();
    $year = date('y');
    $alert_times = array();
    $alert_messages = array();
    $offset = 60 * 3;
    $headers = array();
    $rows = array();
    $satellite_lists = array();

    foreach ($files as $file) {
        $parts = explode('.', basename($file));
        $satellite_lists[] = $parts[0];
    }
    sort($satellite_lists, SORT_NATURAL);

    $mag_options = astro_numeric_options(array(1.0, 1.5, 2.0, 2.5, 3.0, 4.0, 5.0, 6.0), (float) $mag);
    $max_options = astro_numeric_options(array(10, 20, 30, 40, 50, 75, 100), $max);

    if ($sat !== '') {
        $fname = astro_path('satellite/ha/' . basename($sat) . '.tsv');
        if (file_exists($fname)) {
            $fh = fopen($fname, 'r') or die("Cannot open file $fname!\n");
            $count = 0;
            while (!feof($fh)) {
                $line = fgets($fh);
                if ($line === false) {
                    continue;
                }
                $fields = explode("\t", rtrim($line));
                if (count($fields) <= 1) {
                    continue;
                }

                if (empty($headers)) {
                    $headers = $fields;
                    $headers[1] = 'Date';
                    $headers = array_map('astro_table_text_cell', $headers);
                    continue;
                }

                $date_parts = explode('-', $fields[1]);
                if (count($date_parts) !== 2) {
                    continue;
                }
                $month_day = $date_parts[0];
                $start_time = strtotime($month_day . '/' . $year . ' ' . $date_parts[1]);
                if ($start_time < $current || (float) $fields[2] >= $mag) {
                    continue;
                }
                if ($count >= $max) {
                    break;
                }

                if ($start_time - $offset > $current) {
                    $alert_messages[] = $fields[0] . ' , Mag: ' . $fields[2];
                    $alert_times[] = $start_time - $offset;
                }

                $display = $fields;
                $display[1] = date('D, n/j', $start_time);
                $sort_row = array(
                    $display[0],
                    $start_time,
                    (float) $display[2],
                    astro_time_to_seconds($display[3]),
                    (int) preg_replace('/[^0-9\-]/', '', $display[4]),
                    $display[5],
                    astro_time_to_seconds($display[6]),
                    (int) preg_replace('/[^0-9\-]/', '', $display[7]),
                    $display[8],
                    astro_time_to_seconds($display[9]),
                    (int) preg_replace('/[^0-9\-]/', '', $display[10]),
                    $display[11],
                );
                $display_row = array();
                foreach ($display as $column => $value) {
                    $display_row[] = astro_table_text_cell($value, array('sort_value' => $sort_row[$column] ?? $value));
                }
                $rows[] = $display_row;
                $count++;
            }
            fclose($fh);
        }
    }

    $formatted_mag_options = array();
    foreach ($mag_options as $option) {
        $formatted = number_format((float) $option, 1, '.', '');
        $formatted_mag_options[$formatted] = $formatted;
    }

    ob_start();
    echo '<form class="filter-form filter-form--compact" method="get" action="/table.php">';
    echo '<input type="hidden" name="tbm" value="satellite_vis">';
    echo '<div class="filter-field filter-field--inline">';
    echo '<label class="filter-field__label" for="satellite-vis-name">Visible Pass List</label>';
    echo '<select class="filter-select" id="satellite-vis-name" name="sat" onchange="this.form.submit()">';
    echo astro_select_option('', 'Choose list', $sat === '');
    foreach ($satellite_lists as $name) {
        echo astro_select_option($name, $name, $sat === $name);
    }
    echo '</select>';
    echo '</div>';
    echo astro_inline_select_field('satellite-vis-mag', 'mag', 'Max Magnitude', $formatted_mag_options, number_format((float) $mag, 1, '.', ''));
    echo astro_inline_select_field('satellite-vis-max', 'max', 'Rows', array_combine($max_options, $max_options), $max);
    echo '<noscript><button class="filter-submit" type="submit">Apply</button></noscript>';
    echo '</form>';
    $controls_html = ob_get_clean();

    if (empty($headers)) {
        $sections = array(
            array(
                'type' => 'html',
                'html' => '<p class="page-note">Choose a visible-pass list from the dropdown to view results.</p>',
            ),
        );
    } else {
        $sections = array(
            array(
                'type' => 'sortable_table',
                'headers' => $headers,
                'rows' => $rows,
                'options' => array('empty_message' => 'No visible passes match the current filters.'),
            ),
        );
    }

    return array(
        'title' => 'Satellite Visible',
        'controls_html' => $controls_html,
        'sections' => $sections,
        'scripts' => array(
            astro_satellite_counteralert_script($alert_times, $alert_messages, 'config/tgsatvisalt.off', $max_alert),
        ),
    );
};
