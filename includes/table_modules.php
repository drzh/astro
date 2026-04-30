<?php

require_once __DIR__ . '/table.php';
require_once __DIR__ . '/ui.php';

if (!function_exists('astro_table_module_registry')) {
    function astro_table_module_registry()
    {
        return array(
            'satellite_ham' => array(
                'label' => 'Satellite Ham',
                'file' => astro_path('table/modules/satellite_ham.php'),
            ),
            'satellite_vis' => array(
                'label' => 'Satellite Visible',
                'file' => astro_path('table/modules/satellite_vis.php'),
            ),
        );
    }
}

if (!function_exists('astro_load_table_module')) {
    function astro_load_table_module($module_name, $request)
    {
        $module_name = strtolower(trim((string) $module_name));
        $registry = astro_table_module_registry();
        if (!array_key_exists($module_name, $registry)) {
            throw new InvalidArgumentException('Unknown table module: ' . $module_name);
        }

        $module_file = $registry[$module_name]['file'];
        if (!is_file($module_file)) {
            throw new RuntimeException('Table module file is missing: ' . $module_name);
        }

        $factory = require $module_file;
        if (!is_callable($factory)) {
            throw new RuntimeException('Table module is not callable: ' . $module_name);
        }

        $payload = $factory($request);
        if (!is_array($payload)) {
            throw new RuntimeException('Table module did not return a payload: ' . $module_name);
        }

        $payload['module'] = $module_name;
        if (empty($payload['title'])) {
            $payload['title'] = $registry[$module_name]['label'];
        }

        return $payload;
    }
}

if (!function_exists('astro_render_table_module')) {
    function astro_render_table_module($payload)
    {
        if (!empty($payload['controls_html'])) {
            echo '<section class="panel">', $payload['controls_html'], '</section>';
        }

        if (!empty($payload['before_html'])) {
            echo $payload['before_html'];
        }

        $sections = $payload['sections'] ?? array();
        if (empty($sections) && isset($payload['headers'])) {
            $sections[] = array(
                'type' => 'sortable_table',
                'headers' => $payload['headers'],
                'rows' => $payload['rows'] ?? array(),
                'sort_values' => $payload['sort_values'] ?? array(),
                'options' => $payload['table_options'] ?? array(),
            );
        }

        foreach ($sections as $section) {
            astro_render_table_module_section($section);
        }

        if (!empty($payload['after_html'])) {
            echo $payload['after_html'];
        }

        foreach (($payload['scripts'] ?? array()) as $script_html) {
            echo $script_html;
        }
    }
}

if (!function_exists('astro_render_table_module_section')) {
    function astro_render_table_module_section($section)
    {
        $wrap = $section['wrap'] ?? true;
        if ($wrap) {
            echo '<section class="panel">';
        }

        if (!empty($section['title'])) {
            echo '<h2 class="panel-title">', htmlspecialchars((string) $section['title'], ENT_QUOTES, 'UTF-8'), '</h2>';
        }

        $type = $section['type'] ?? 'sortable_table';
        if ($type === 'html') {
            echo $section['html'] ?? '';
        } elseif ($type === 'plain_table') {
            render_plain_table(
                $section['headers'] ?? array(),
                $section['rows'] ?? array(),
                $section['options'] ?? array()
            );
        } else {
            render_sortable_table(
                $section['headers'] ?? array(),
                $section['rows'] ?? array(),
                $section['sort_values'] ?? array(),
                $section['options'] ?? array()
            );
        }

        if ($wrap) {
            echo '</section>';
        }
    }
}

if (!function_exists('astro_table_module_redirect')) {
    function astro_table_module_redirect($module_name, $params = array())
    {
        $params['tbm'] = $module_name;
        header('Location: /table.php?' . http_build_query($params));
        exit;
    }
}
