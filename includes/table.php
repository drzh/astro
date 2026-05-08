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

if (!function_exists('astro_table_class_list')) {
    function astro_table_class_list($classes)
    {
        $class_list = array();
        foreach ((array) $classes as $class) {
            if (is_array($class)) {
                $nested = astro_table_class_list($class);
                if ($nested !== '') {
                    $class_list[] = $nested;
                }
                continue;
            }

            $class = trim((string) $class);
            if ($class !== '') {
                $class_list[] = preg_replace('/\s+/', ' ', $class);
            }
        }

        return trim(implode(' ', $class_list));
    }
}

if (!function_exists('astro_table_style_value')) {
    function astro_table_style_value($style)
    {
        if (is_array($style)) {
            $declarations = array();
            foreach ($style as $property => $value) {
                if ($value === null || $value === false) {
                    continue;
                }

                if (is_int($property)) {
                    if (is_array($value)) {
                        $nested_style = astro_table_style_value($value);
                        if ($nested_style !== '') {
                            $declarations[] = $nested_style;
                        }
                        continue;
                    }
                    $declaration = trim((string) $value);
                } else {
                    $property = trim((string) $property);
                    if (!preg_match('/^[a-zA-Z][-a-zA-Z0-9]*$/', $property)) {
                        continue;
                    }
                    if (is_array($value)) {
                        continue;
                    }
                    $declaration = $property . ': ' . trim((string) $value);
                }

                if ($declaration !== '') {
                    $declarations[] = rtrim($declaration, ';') . ';';
                }
            }

            return implode(' ', $declarations);
        }

        return trim((string) $style);
    }
}

if (!function_exists('astro_table_attributes')) {
    function astro_table_attributes($attributes = array(), $classes = array(), $style = '')
    {
        if (!is_array($attributes)) {
            $attributes = array();
        }

        if (array_key_exists('attributes', $attributes) && is_array($attributes['attributes'])) {
            $attributes = array_merge($attributes['attributes'], $attributes);
            unset($attributes['attributes']);
        }
        if (array_key_exists('attrs', $attributes) && is_array($attributes['attrs'])) {
            $attributes = array_merge($attributes['attrs'], $attributes);
            unset($attributes['attrs']);
        }
        if (array_key_exists('data', $attributes) && is_array($attributes['data'])) {
            foreach ($attributes['data'] as $key => $value) {
                $attributes['data-' . str_replace('_', '-', (string) $key)] = $value;
            }
            unset($attributes['data']);
        }

        if (array_key_exists('class', $attributes)) {
            $classes = array($classes, $attributes['class']);
            unset($attributes['class']);
        }
        if (array_key_exists('style', $attributes)) {
            $style = array($style, $attributes['style']);
            unset($attributes['style']);
        }

        $class_value = astro_table_class_list($classes);
        $style_value = astro_table_style_value($style);
        $html = '';

        if ($class_value !== '') {
            $html .= ' class="' . htmlspecialchars($class_value, ENT_QUOTES, 'UTF-8') . '"';
        }
        if ($style_value !== '') {
            $html .= ' style="' . htmlspecialchars($style_value, ENT_QUOTES, 'UTF-8') . '"';
        }

        foreach ($attributes as $name => $value) {
            if ($value === null || $value === false) {
                continue;
            }

            $name = trim((string) $name);
            if (!preg_match('/^[a-zA-Z_:][-a-zA-Z0-9_:.]*$/', $name) || stripos($name, 'on') === 0) {
                continue;
            }

            if ($value === true) {
                $html .= ' ' . $name;
            } else {
                $html .= ' ' . $name . '="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }

        return $html;
    }
}

if (!function_exists('astro_table_cell_html')) {
    function astro_table_cell_html($cell)
    {
        if (!is_array($cell)) {
            return (string) $cell;
        }

        foreach (array('html', 'content') as $key) {
            if (array_key_exists($key, $cell)) {
                return (string) $cell[$key];
            }
        }

        foreach (array('text', 'value', 'label') as $key) {
            if (array_key_exists($key, $cell)) {
                return htmlspecialchars((string) $cell[$key], ENT_QUOTES, 'UTF-8');
            }
        }

        return '';
    }
}

if (!function_exists('astro_table_text_cell')) {
    function astro_table_text_cell($text, $options = array())
    {
        if (!is_array($options)) {
            $options = array();
        }
        $options['text'] = $text;
        return $options;
    }
}

if (!function_exists('astro_table_html_cell')) {
    function astro_table_html_cell($html, $options = array())
    {
        if (!is_array($options)) {
            $options = array();
        }
        $options['html'] = $html;
        return $options;
    }
}

if (!function_exists('astro_table_text_row')) {
    function astro_table_text_row($values, $sort_values = array())
    {
        $row = array();
        foreach ((array) $values as $column => $value) {
            $options = array();
            if (is_array($sort_values) && array_key_exists($column, $sort_values)) {
                $options['sort_value'] = $sort_values[$column];
            }
            $row[] = astro_table_text_cell($value, $options);
        }

        return $row;
    }
}

if (!function_exists('astro_table_cell_text')) {
    function astro_table_cell_text($cell)
    {
        return trim(preg_replace(
            '/\s+/',
            ' ',
            html_entity_decode(strip_tags(astro_table_cell_html($cell)), ENT_QUOTES | ENT_HTML5, 'UTF-8')
        ));
    }
}

if (!function_exists('astro_table_cell_sort_source')) {
    function astro_table_cell_sort_source($cell, $fallback = null)
    {
        if (is_array($cell)) {
            if (array_key_exists('sort_value', $cell)) {
                return $cell['sort_value'];
            }
            if (array_key_exists('sort', $cell)) {
                return $cell['sort'];
            }
        }

        if ($fallback !== null) {
            return $fallback;
        }

        return astro_table_cell_text($cell);
    }
}

if (!function_exists('astro_table_cell_attributes')) {
    function astro_table_cell_attributes($cell, $default_class = '', $extra_attributes = array())
    {
        $classes = array($default_class);
        $style = '';
        $attributes = array();

        if (is_array($cell)) {
            if (array_key_exists('class', $cell)) {
                $classes[] = $cell['class'];
            }
            if (array_key_exists('style', $cell)) {
                $style = $cell['style'];
            }
            foreach (array('attrs', 'attributes') as $key) {
                if (array_key_exists($key, $cell) && is_array($cell[$key])) {
                    $attributes = array_merge($attributes, $cell[$key]);
                }
            }
            if (array_key_exists('data', $cell) && is_array($cell['data'])) {
                $existing_data = isset($attributes['data']) && is_array($attributes['data']) ? $attributes['data'] : array();
                $attributes['data'] = $existing_data + $cell['data'];
            }
            foreach (array('colspan', 'rowspan', 'scope') as $key) {
                if (array_key_exists($key, $cell)) {
                    $attributes[$key] = $cell[$key];
                }
            }
        }

        $attributes = array_merge($attributes, $extra_attributes);
        return astro_table_attributes($attributes, $classes, $style);
    }
}

if (!function_exists('astro_table_row_cells')) {
    function astro_table_row_cells($row)
    {
        if (is_array($row) && array_key_exists('cells', $row) && is_array($row['cells'])) {
            return $row['cells'];
        }

        return is_array($row) ? $row : array($row);
    }
}

if (!function_exists('astro_table_row_sort_values')) {
    function astro_table_row_sort_values($row)
    {
        if (is_array($row) && array_key_exists('sort_values', $row) && is_array($row['sort_values'])) {
            return $row['sort_values'];
        }

        return array();
    }
}

if (!function_exists('astro_table_row_attributes')) {
    function astro_table_row_attributes($row)
    {
        if (!is_array($row) || !array_key_exists('cells', $row)) {
            return '';
        }

        $attributes = array();
        foreach (array('attrs', 'attributes') as $key) {
            if (array_key_exists($key, $row) && is_array($row[$key])) {
                $attributes = array_merge($attributes, $row[$key]);
            }
        }
        if (array_key_exists('data', $row) && is_array($row['data'])) {
            $existing_data = isset($attributes['data']) && is_array($attributes['data']) ? $attributes['data'] : array();
            $attributes['data'] = $existing_data + $row['data'];
        }

        return astro_table_attributes(
            $attributes,
            array_key_exists('class', $row) ? $row['class'] : '',
            array_key_exists('style', $row) ? $row['style'] : ''
        );
    }
}

if (!function_exists('astro_table_option_attributes')) {
    function astro_table_option_attributes($options, $prefix, $base_class = '')
    {
        $attributes = array();
        foreach (array($prefix . '_attrs', $prefix . '_attributes') as $key) {
            if (array_key_exists($key, $options) && is_array($options[$key])) {
                $attributes = array_merge($attributes, $options[$key]);
            }
        }

        return astro_table_attributes(
            $attributes,
            array($base_class, $options[$prefix . '_class'] ?? ''),
            $options[$prefix . '_style'] ?? ''
        );
    }
}

if (!function_exists('render_plain_table')) {
    function render_plain_table($headers = array(), $rows = array(), $options = array())
    {
        $empty_message = $options['empty_message'] ?? '';

        echo '<div', astro_table_option_attributes($options, 'wrapper', 'table-wrap'), '>';
        echo '<table', astro_table_option_attributes($options, 'table', 'table1'), '>';
        if (!empty($headers)) {
            echo '<thead><tr>';
            foreach ($headers as $header) {
                $label = trim(strip_tags(astro_table_cell_html($header))) === '' ? '&nbsp;' : astro_table_cell_html($header);
                $attributes = array('scope' => is_array($header) && array_key_exists('scope', $header) ? $header['scope'] : 'col');
                echo '<th', astro_table_cell_attributes($header, '', $attributes), '>';
                echo '<span class="table-head-cell">', $label, '</span></th>';
            }
            echo '</tr></thead>';
        }

        echo '<tbody>';
        if (!empty($rows)) {
            foreach ($rows as $row_index => $row) {
                $cells = astro_table_row_cells($row);
                $cell_class = 'td' . (($row_index + 1) % 2);
                echo '<tr', astro_table_row_attributes($row), '>';
                foreach ($cells as $cell) {
                    echo '<td', astro_table_cell_attributes($cell, $cell_class), '>', astro_table_cell_html($cell), '</td>';
                }
                echo '</tr>';
            }
        } elseif ($empty_message !== '') {
            $colspan = max(1, count($headers));
            echo '<tr><td', astro_table_attributes(array('colspan' => $colspan), 'td1'), '>', htmlspecialchars((string) $empty_message, ENT_QUOTES, 'UTF-8'), '</td></tr>';
        }
        echo "</tbody></table></div>\n";
    }
}

if (!function_exists('render_sortable_table')) {
    function render_sortable_table($headers = array(), $rows = array(), $sort_values = array(), $options = array())
    {
        $empty_message = $options['empty_message'] ?? '';
        $sorted_column = $options['sorted_column'] ?? null;
        $sort_order = astro_normalize_table_sort_order($options['sort_order'] ?? 'asc');

        $options['table_class'] = array('sortable', $options['table_class'] ?? '');
        echo '<div', astro_table_option_attributes($options, 'wrapper', 'table-wrap'), '>';
        echo '<table', astro_table_option_attributes($options, 'table', 'table1'), '>';
        if (!empty($headers)) {
            echo '<thead><tr>';
            foreach ($headers as $column => $header) {
                $label = trim(strip_tags(astro_table_cell_html($header))) === '' ? '&nbsp;' : astro_table_cell_html($header);
                $column_is_sorted = $sorted_column !== null && (int) $sorted_column === (int) $column;
                $column_sort_order = $column_is_sorted ? $sort_order : 'none';
                $aria_sort = 'none';
                if ($column_sort_order === 'asc') {
                    $aria_sort = 'ascending';
                } elseif ($column_sort_order === 'desc') {
                    $aria_sort = 'descending';
                }

                $header_attributes = array(
                    'scope' => is_array($header) && array_key_exists('scope', $header) ? $header['scope'] : 'col',
                    'aria-sort' => $aria_sort,
                );
                echo '<th', astro_table_cell_attributes($header, '', $header_attributes), '>';
                echo '<button', astro_table_attributes(array(
                    'type' => 'button',
                    'data-direction' => $column_sort_order,
                    'data-column' => $column,
                ), array('sort-button', is_array($header) && array_key_exists('button_class', $header) ? $header['button_class'] : ''), is_array($header) && array_key_exists('button_style', $header) ? $header['button_style'] : ''), '>';
                echo '<span class="sort-label">', $label, '</span><span class="sort-indicator" aria-hidden="true"></span>';
                echo '</button></th>';
            }
            echo '</tr></thead>';
        }

        echo '<tbody>';
        if (!empty($rows)) {
            foreach ($rows as $row_index => $row) {
                $cells = astro_table_row_cells($row);
                $row_sort_values = astro_table_row_sort_values($row);
                $cell_class = 'td' . (($row_index + 1) % 2);
                echo '<tr', astro_table_row_attributes($row), '>';
                foreach ($cells as $column => $cell) {
                    $sort_value = null;
                    if (array_key_exists($column, $row_sort_values)) {
                        $sort_value = $row_sort_values[$column];
                    } elseif (isset($sort_values[$row_index]) && is_array($sort_values[$row_index]) && array_key_exists($column, $sort_values[$row_index])) {
                        $sort_value = $sort_values[$row_index][$column];
                    }
                    $sort_value = astro_table_cell_sort_source($cell, $sort_value);
                    $attributes = array();
                    if ($sort_value !== null && $sort_value !== '') {
                        $attributes['data-sort-value'] = $sort_value;
                    }

                    echo '<td', astro_table_cell_attributes($cell, $cell_class, $attributes), '>', astro_table_cell_html($cell), '</td>';
                }
                echo '</tr>';
            }
        } elseif ($empty_message !== '') {
            $colspan = max(1, count($headers));
            echo '<tr><td', astro_table_attributes(array('colspan' => $colspan), 'td1'), '>', htmlspecialchars((string) $empty_message, ENT_QUOTES, 'UTF-8'), '</td></tr>';
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
        $text = is_array($value)
            ? astro_table_cell_text($value)
            : html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return strtolower(trim(preg_replace('/\s+/', ' ', $text)));
    }
}

if (!function_exists('astro_table_sort_value')) {
    function astro_table_sort_value($value)
    {
        $text = is_array($value)
            ? astro_table_cell_text($value)
            : trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
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
            $left_cells = astro_table_row_cells($left['row']);
            $right_cells = astro_table_row_cells($right['row']);
            $left_value = astro_table_sort_value(astro_table_cell_sort_source($left_cells[$column] ?? ''));
            $right_value = astro_table_sort_value(astro_table_cell_sort_source($right_cells[$column] ?? ''));
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

if (!function_exists('astro_escape_table_cell')) {
    function astro_escape_table_cell($cell)
    {
        if (is_array($cell)) {
            foreach (array('html', 'content') as $key) {
                if (array_key_exists($key, $cell)) {
                    $cell[$key] = htmlspecialchars((string) $cell[$key], ENT_QUOTES, 'UTF-8');
                }
            }
            return $cell;
        }

        return htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('astro_escape_table_rows')) {
    function astro_escape_table_rows($rows)
    {
        foreach ($rows as $row_index => $row) {
            $is_structured_row = is_array($row) && array_key_exists('cells', $row) && is_array($row['cells']);
            $cells = astro_table_row_cells($row);
            foreach ($cells as $column => $cell) {
                if ($is_structured_row) {
                    $rows[$row_index]['cells'][$column] = astro_escape_table_cell($cell);
                } else {
                    $rows[$row_index][$column] = astro_escape_table_cell($cell);
                }
            }
        }

        return $rows;
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

            $escape_cells = true;
            if (array_key_exists('allow_html', $options)) {
                $escape_cells = !$options['allow_html'];
            }
            if (array_key_exists('escape_cells', $options)) {
                $escape_cells = (bool) $options['escape_cells'];
            }
            if ($escape_cells) {
                $headers = astro_escape_table_rows(array($headers))[0] ?? array();
                $rows = astro_escape_table_rows($rows);
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
