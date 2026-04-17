<?php

require_once __DIR__ . '/bootstrap.php';

if (!function_exists('astro_nav_item')) {
    function astro_nav_item($href, $label, $active, $link_class = 'menu-state-link', $active_class = 'citem')
    {
        $label = htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8');
        if ($active) {
            return '<span class="' . htmlspecialchars((string) $active_class, ENT_QUOTES, 'UTF-8') . '">' . $label . '</span>';
        }

        return '<a class="' . htmlspecialchars((string) $link_class, ENT_QUOTES, 'UTF-8') . '" href="' . htmlspecialchars((string) $href, ENT_QUOTES, 'UTF-8') . '">' . $label . '</a>';
    }
}

if (!function_exists('astro_select_option')) {
    function astro_select_option($value, $label, $selected = false)
    {
        return '<option value="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '"' . ($selected ? ' selected' : '') . '>' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '</option>';
    }
}

if (!function_exists('astro_inline_select_field')) {
    function astro_inline_select_field($id, $name, $label, $options, $current)
    {
        $html = '<div class="filter-field filter-field--inline">';
        $html .= '<label class="filter-field__label" for="' . htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '</label>';
        $html .= '<select class="filter-select" id="' . htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') . '" name="' . htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8') . '" onchange="this.form.submit()">';
        foreach ($options as $value => $option_label) {
            $html .= astro_select_option($value, $option_label, (string) $current === (string) $value);
        }
        $html .= '</select>';
        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('astro_render_cloud_script')) {
    function astro_render_cloud_script()
    {
        echo '<script src="cloud.js"></script>';
    }
}

if (!function_exists('astro_numeric_options')) {
    function astro_numeric_options($values, $current, $sort_flag = SORT_NUMERIC)
    {
        if (!in_array($current, $values, true)) {
            $values[] = $current;
        }
        sort($values, $sort_flag);
        return $values;
    }
}
