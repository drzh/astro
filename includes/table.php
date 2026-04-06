<?php

require_once __DIR__ . '/bootstrap.php';

if (!function_exists('astro_duration_to_seconds')) {
    function astro_duration_to_seconds($duration)
    {
        $duration = trim((string) $duration);
        if ($duration === '') {
            return '';
        }

        $parts = explode(':', $duration);
        if (count($parts) !== 3) {
            return $duration;
        }

        return ((int) $parts[0] * 3600) + ((int) $parts[1] * 60) + (int) $parts[2];
    }
}

if (!function_exists('astro_time_to_seconds')) {
    function astro_time_to_seconds($time)
    {
        $time = trim((string) $time);
        if ($time === '') {
            return '';
        }

        $parts = explode(':', $time);
        if (count($parts) < 2 || count($parts) > 3) {
            return $time;
        }

        return ((int) $parts[0] * 3600) + ((int) $parts[1] * 60) + (int) ($parts[2] ?? 0);
    }
}

if (!function_exists('render_plain_table')) {
    function render_plain_table($headers = array(), $rows = array(), $options = array())
    {
        $table_class = trim('table1 ' . ($options['table_class'] ?? ''));
        $empty_message = $options['empty_message'] ?? '';

        echo "<div class='table-wrap'><table class='", $table_class, "'>";
        if (!empty($headers)) {
            echo '<thead><tr>';
            foreach ($headers as $header) {
                $label = trim(strip_tags((string) $header)) === '' ? '&nbsp;' : $header;
                echo '<th scope="col"><span class="table-head-cell">', $label, '</span></th>';
            }
            echo '</tr></thead>';
        }

        echo '<tbody>';
        if (!empty($rows)) {
            foreach ($rows as $row_index => $row) {
                $cell_class = 'td' . (($row_index + 1) % 2);
                echo '<tr>';
                foreach ($row as $cell) {
                    echo '<td class="', $cell_class, '">', $cell, '</td>';
                }
                echo '</tr>';
            }
        } elseif ($empty_message !== '') {
            $colspan = max(1, count($headers));
            echo '<tr><td class="td1" colspan="', $colspan, '">', $empty_message, '</td></tr>';
        }
        echo "</tbody></table></div>\n";
    }
}

if (!function_exists('render_sortable_table')) {
    function render_sortable_table($headers = array(), $rows = array(), $sort_values = array(), $options = array())
    {
        $table_class = trim('table1 sortable ' . ($options['table_class'] ?? ''));
        $empty_message = $options['empty_message'] ?? '';

        echo "<div class='table-wrap'><table class='", $table_class, "'>";
        if (!empty($headers)) {
            echo '<thead><tr>';
            foreach ($headers as $column => $header) {
                $label = trim(strip_tags((string) $header)) === '' ? '&nbsp;' : $header;
                echo '<th scope="col" aria-sort="none">';
                echo '<button type="button" class="sort-button" data-direction="none" data-column="', $column, '">';
                echo '<span class="sort-label">', $label, '</span><span class="sort-indicator" aria-hidden="true"></span>';
                echo '</button></th>';
            }
            echo '</tr></thead>';
        }

        echo '<tbody>';
        if (!empty($rows)) {
            foreach ($rows as $row_index => $row) {
                $cell_class = 'td' . (($row_index + 1) % 2);
                echo '<tr>';
                foreach ($row as $column => $cell) {
                    echo '<td class="', $cell_class, '"';
                    if (isset($sort_values[$row_index][$column]) && $sort_values[$row_index][$column] !== '') {
                        echo ' data-sort-value="', htmlspecialchars((string) $sort_values[$row_index][$column], ENT_QUOTES, 'UTF-8'), '"';
                    }
                    echo '>', $cell, '</td>';
                }
                echo '</tr>';
            }
        } elseif ($empty_message !== '') {
            $colspan = max(1, count($headers));
            echo '<tr><td class="td1" colspan="', $colspan, '">', $empty_message, '</td></tr>';
        }
        echo "</tbody></table></div>\n";
    }
}

if (!function_exists('display_table_from_tsv')) {
    function display_table_from_tsv($tb = '', $display_date = 0)
    {
        if (isset($tb)) {
            $fh = fopen($tb, 'r') or die("Cannot open file!\n");
            $headers = array();
            $rows = array();

            while (!feof($fh)) {
                $e = explode("\t", fgets($fh));
                if (count($e) == 1 && trim($e[0]) === '') {
                    continue;
                }

                if (empty($headers)) {
                    $headers = array_map('rtrim', $e);
                } else {
                    $rows[] = array_map('rtrim', $e);
                }
            }
            fclose($fh);

            render_sortable_table($headers, $rows);

            if ($display_date == 1) {
                $lastUpdated = filemtime($tb);
                echo "<small class='smalltext1'>Last Updated: " . date("Y-m-d H:i:s", $lastUpdated) . "</small>\n";
            }
        }
    }
}

if (!function_exists('get_last_updated_time')) {
    function get_last_updated_time($para_tb)
    {
        if (isset($para_tb)) {
            $lastUpdated = filemtime($para_tb);
            return date("Y-m-d H:i:s", $lastUpdated);
        }
        return null;
    }
}
