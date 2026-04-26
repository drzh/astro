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
        $sorted_column = $options['sorted_column'] ?? null;
        $sort_order = astro_normalize_table_sort_order($options['sort_order'] ?? 'asc');

        echo "<div class='table-wrap'><table class='", $table_class, "'>";
        if (!empty($headers)) {
            echo '<thead><tr>';
            foreach ($headers as $column => $header) {
                $label = trim(strip_tags((string) $header)) === '' ? '&nbsp;' : $header;
                $column_is_sorted = $sorted_column !== null && (int) $sorted_column === (int) $column;
                $column_sort_order = $column_is_sorted ? $sort_order : 'none';
                $aria_sort = 'none';
                if ($column_sort_order === 'asc') {
                    $aria_sort = 'ascending';
                } elseif ($column_sort_order === 'desc') {
                    $aria_sort = 'descending';
                }

                echo '<th scope="col" aria-sort="', $aria_sort, '">';
                echo '<button type="button" class="sort-button" data-direction="', $column_sort_order, '" data-column="', htmlspecialchars((string) $column, ENT_QUOTES, 'UTF-8'), '">';
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

if (!function_exists('astro_normalize_table_sort_order')) {
    function astro_normalize_table_sort_order($sort_order)
    {
        $sort_order = strtolower(trim((string) $sort_order));
        if (in_array($sort_order, array('desc', 'descending', 'decend', 'decending'), true)) {
            return 'desc';
        }

        return 'asc';
    }
}

if (!function_exists('astro_table_sort_column_index')) {
    function astro_table_sort_column_index($headers, $sort_column)
    {
        $sort_column = trim((string) $sort_column);
        if ($sort_column === '') {
            return null;
        }

        if (preg_match('/^\d+$/', $sort_column)) {
            $column = (int) $sort_column;
            return array_key_exists($column, $headers) ? $column : null;
        }

        $normalized_sort_column = astro_normalize_table_sort_label($sort_column);
        foreach ($headers as $column => $header) {
            if (astro_normalize_table_sort_label($header) === $normalized_sort_column) {
                return (int) $column;
            }
        }

        return null;
    }
}

if (!function_exists('astro_normalize_table_sort_label')) {
    function astro_normalize_table_sort_label($value)
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return strtolower(trim(preg_replace('/\s+/', ' ', $text)));
    }
}

if (!function_exists('astro_table_sort_value')) {
    function astro_table_sort_value($value)
    {
        $text = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        if ($text === '') {
            return array('type' => 'empty', 'value' => '');
        }

        $numeric_text = str_replace(array(',', '°', '%'), '', $text);
        if (preg_match('/^[+-]?\d+(\.\d+)?$/', $numeric_text)) {
            return array('type' => 'number', 'value' => (float) $numeric_text);
        }

        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $text, $matches)) {
            $seconds = ((int) $matches[1] * 3600) + ((int) $matches[2] * 60) + (int) ($matches[3] ?? 0);
            return array('type' => 'number', 'value' => $seconds);
        }

        $date_value = strtotime($text);
        if ($date_value !== false) {
            return array('type' => 'number', 'value' => $date_value);
        }

        return array('type' => 'text', 'value' => strtolower($text));
    }
}

if (!function_exists('astro_compare_table_sort_values')) {
    function astro_compare_table_sort_values($left, $right)
    {
        if ($left['type'] === 'empty' && $right['type'] === 'empty') {
            return 0;
        }
        if ($left['type'] === 'empty') {
            return 1;
        }
        if ($right['type'] === 'empty') {
            return -1;
        }
        if ($left['type'] === 'number' && $right['type'] === 'number') {
            return $left['value'] <=> $right['value'];
        }

        return strnatcasecmp((string) $left['value'], (string) $right['value']);
    }
}

if (!function_exists('astro_sort_table_rows')) {
    function astro_sort_table_rows($headers, $rows, $sort_column, $sort_order)
    {
        $column = astro_table_sort_column_index($headers, $sort_column);
        if ($column === null) {
            return array($rows, null);
        }

        $direction = astro_normalize_table_sort_order($sort_order);
        $decorated_rows = array();
        foreach ($rows as $index => $row) {
            $decorated_rows[] = array('index' => $index, 'row' => $row);
        }

        usort($decorated_rows, function ($left, $right) use ($column, $direction) {
            $left_value = astro_table_sort_value($left['row'][$column] ?? '');
            $right_value = astro_table_sort_value($right['row'][$column] ?? '');
            $result = astro_compare_table_sort_values($left_value, $right_value);
            if ($result !== 0) {
                return $direction === 'asc' ? $result : -$result;
            }

            return $left['index'] <=> $right['index'];
        });

        $sorted_rows = array();
        foreach ($decorated_rows as $decorated_row) {
            $sorted_rows[] = $decorated_row['row'];
        }

        return array($sorted_rows, $column);
    }
}

if (!function_exists('astro_normalize_table_positive_int')) {
    function astro_normalize_table_positive_int($value, $default, $max = null)
    {
        if (!is_numeric($value)) {
            return $default;
        }

        $number = (int) $value;
        if ($number < 1) {
            return $default;
        }
        if ($max !== null && $number > $max) {
            return $max;
        }

        return $number;
    }
}

if (!function_exists('astro_table_page_href')) {
    function astro_table_page_href($params, $page)
    {
        $params['page'] = $page;
        return '?' . http_build_query($params);
    }
}

if (!function_exists('render_table_pagination')) {
    function render_table_pagination($page, $total_pages, $total_rows, $per_page, $params)
    {
        if ($total_pages <= 1) {
            return;
        }

        $params['per_page'] = $per_page;
        unset($params['page']);
        $window_start = max(1, $page - 2);
        $window_end = min($total_pages, $page + 2);

        echo '<nav class="table-pagination" aria-label="Table pages">';
        echo '<span class="table-pagination__status">Page ', $page, ' of ', $total_pages, ' · ', $total_rows, ' records</span>';

        if ($page > 1) {
            echo '<a class="table-pagination__link" href="', htmlspecialchars(astro_table_page_href($params, 1), ENT_QUOTES, 'UTF-8'), '">First</a>';
            echo '<a class="table-pagination__link" href="', htmlspecialchars(astro_table_page_href($params, $page - 1), ENT_QUOTES, 'UTF-8'), '">Prev</a>';
        }

        for ($index = $window_start; $index <= $window_end; $index++) {
            if ($index === $page) {
                echo '<span class="table-pagination__current" aria-current="page">', $index, '</span>';
            } else {
                echo '<a class="table-pagination__link" href="', htmlspecialchars(astro_table_page_href($params, $index), ENT_QUOTES, 'UTF-8'), '">', $index, '</a>';
            }
        }

        if ($page < $total_pages) {
            echo '<a class="table-pagination__link" href="', htmlspecialchars(astro_table_page_href($params, $page + 1), ENT_QUOTES, 'UTF-8'), '">Next</a>';
            echo '<a class="table-pagination__link" href="', htmlspecialchars(astro_table_page_href($params, $total_pages), ENT_QUOTES, 'UTF-8'), '">Last</a>';
        }

        echo "</nav>\n";
    }
}

if (!function_exists('display_table_from_tsv')) {
    function display_table_from_tsv($tb = '', $display_date = 0, $options = array())
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

            $table_options = array();
            if (isset($options['sort_column']) && $options['sort_column'] !== '') {
                list($rows, $sorted_column) = astro_sort_table_rows(
                    $headers,
                    $rows,
                    $options['sort_column'],
                    $options['sort_order'] ?? 'asc'
                );

                if ($sorted_column !== null) {
                    $table_options['sorted_column'] = $sorted_column;
                    $table_options['sort_order'] = astro_normalize_table_sort_order($options['sort_order'] ?? 'asc');
                }
            }

            $pagination = null;
            if (!empty($options['paginate'])) {
                $per_page = astro_normalize_table_positive_int($options['per_page'] ?? 50, 50, 1000);
                $total_rows = count($rows);
                $total_pages = max(1, (int) ceil($total_rows / $per_page));
                $page = min(
                    astro_normalize_table_positive_int($options['page'] ?? 1, 1),
                    $total_pages
                );
                $rows = array_slice($rows, ($page - 1) * $per_page, $per_page);
                $pagination = array(
                    'page' => $page,
                    'total_pages' => $total_pages,
                    'total_rows' => $total_rows,
                    'per_page' => $per_page,
                    'params' => $options['pagination_params'] ?? array(),
                );
            }

            if ($pagination !== null) {
                render_table_pagination(
                    $pagination['page'],
                    $pagination['total_pages'],
                    $pagination['total_rows'],
                    $pagination['per_page'],
                    $pagination['params']
                );
            }

            render_sortable_table($headers, $rows, array(), $table_options);

            if ($pagination !== null) {
                render_table_pagination(
                    $pagination['page'],
                    $pagination['total_pages'],
                    $pagination['total_rows'],
                    $pagination['per_page'],
                    $pagination['params']
                );
            }

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
