<?php

require_once __DIR__ . '/../../includes/table_modules.php';

if (!function_exists('astro_atmos_optics_datasets')) {
    function astro_atmos_optics_datasets()
    {
        return array(
            array(
                'title' => 'Atmospheric Optics',
                'json_path' => astro_path('table/data/atmospheric_optics.json'),
                'json_href' => '/table/data/atmospheric_optics.json',
                'fallback_illumination' => 'solar,lunar',
                'show_cloud_classification' => true,
            ),
        );
    }
}

if (!function_exists('astro_atmos_optics_format_percent')) {
    function astro_atmos_optics_format_percent($value)
    {
        if (!is_numeric($value)) {
            return '0.0%';
        }
        return number_format((float) $value * 100, 1) . '%';
    }
}

if (!function_exists('astro_atmos_optics_probability_band_class')) {
    function astro_atmos_optics_probability_band_class($value)
    {
        if (!is_numeric($value)) {
            return 'atmos-optics-probability--none';
        }

        $probability = max(0.0, min(1.0, (float) $value));
        if ($probability < 0.2) {
            return 'atmos-optics-probability--none';
        }
        if ($probability < 0.4) {
            return 'atmos-optics-probability--blue';
        }
        if ($probability < 0.6) {
            return 'atmos-optics-probability--green';
        }
        if ($probability < 0.8) {
            return 'atmos-optics-probability--orange';
        }
        return 'atmos-optics-probability--red';
    }
}

if (!function_exists('astro_atmos_optics_probability_chip')) {
    function astro_atmos_optics_probability_chip($value, $force_none = false)
    {
        $band_class = $force_none ? 'atmos-optics-probability--none' : astro_atmos_optics_probability_band_class($value);
        $classes = 'atmos-optics-probability ' . $band_class;

        return '<span class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '">' .
            htmlspecialchars(astro_atmos_optics_format_percent($value), ENT_QUOTES, 'UTF-8') .
            '</span>';
    }
}

if (!function_exists('astro_atmos_optics_current_probability')) {
    function astro_atmos_optics_current_probability($value, $reason)
    {
        $chip = astro_atmos_optics_probability_chip($value);
        if (!is_string($reason) || trim($reason) === '') {
            return $chip;
        }

        $reason_text = trim($reason);

        return '<span class="atmos-optics-current-cell" data-atmos-optics-reason="' .
            htmlspecialchars($reason_text, ENT_QUOTES, 'UTF-8') .
            '">' .
            '<span class="atmos-optics-current-trigger" tabindex="0" aria-label="' .
            htmlspecialchars($reason_text, ENT_QUOTES, 'UTF-8') .
            '">' .
            $chip .
            '</span>' .
            '</span>';
    }
}

if (!function_exists('astro_atmos_optics_format_timestamp')) {
    function astro_atmos_optics_format_timestamp($value)
    {
        if (!is_string($value) || trim($value) === '') {
            return '';
        }

        try {
            $time = new DateTimeImmutable($value);
            return $time->format('Y-m-d H:i:s T');
        } catch (Exception $e) {
            return trim($value);
        }
    }
}

if (!function_exists('astro_atmos_optics_format_altitude')) {
    function astro_atmos_optics_format_altitude($value)
    {
        if (!is_numeric($value)) {
            return '';
        }

        return number_format((float) $value, 1) . ' deg';
    }
}

if (!function_exists('astro_atmos_optics_format_meters')) {
    function astro_atmos_optics_format_meters($value)
    {
        if (!is_numeric($value)) {
            return '';
        }

        $meters = (float) $value;
        if (abs($meters) >= 1000.0) {
            return number_format($meters / 1000.0, 1) . ' km';
        }
        return number_format($meters, 0) . ' m';
    }
}

if (!function_exists('astro_atmos_optics_is_lunar_phenomenon')) {
    function astro_atmos_optics_is_lunar_phenomenon($entry)
    {
        if (!is_array($entry) || !isset($entry['id']) || !is_string($entry['id'])) {
            return false;
        }

        $id = $entry['id'];
        return in_array($id, array('lunar_halo', 'paraselenae', 'lunar_pillar', 'lunar_corona', 'moonbow'), true) ||
            strpos($id, 'lunar_') === 0;
    }
}

if (!function_exists('astro_atmos_optics_illumination_label_for_entry')) {
    function astro_atmos_optics_illumination_label_for_entry($entry)
    {
        return astro_atmos_optics_is_lunar_phenomenon($entry) ? 'Lunar' : 'Solar';
    }
}

if (!function_exists('astro_atmos_optics_normalize_payload')) {
    function astro_atmos_optics_normalize_payload($payload, $fallback_illumination)
    {
        $prediction = array();
        $request = array();
        $target = array();
        $sources = array();
        $phenomena = array();
        $clouds = array();
        $mode = '';
        $illumination = $fallback_illumination;
        $prediction_time = '';
        $target_name = '';
        $latitude = null;
        $longitude = null;
        $celestial = array();
        $active_body_label = '';
        $active_body_altitude = null;
        $celestial_details = array();

        if (isset($payload['prediction']) && is_array($payload['prediction'])) {
            $prediction = $payload['prediction'];
        } elseif (isset($payload['request']) && is_array($payload['request'])) {
            $prediction = $payload;
        }
        if (isset($prediction['request']) && is_array($prediction['request'])) {
            $request = $prediction['request'];
        }
        if (isset($payload['target']) && is_array($payload['target'])) {
            $target = $payload['target'];
        }
        if (isset($prediction['sources']) && is_array($prediction['sources'])) {
            $sources = $prediction['sources'];
        }
        if (isset($payload['celestial']) && is_array($payload['celestial'])) {
            $celestial = $payload['celestial'];
        } elseif (isset($prediction['celestial']) && is_array($prediction['celestial'])) {
            $celestial = $prediction['celestial'];
        }
        if (isset($prediction['phenomena']) && is_array($prediction['phenomena'])) {
            $phenomena = $prediction['phenomena'];
        }
        if ($phenomena !== array() && in_array($fallback_illumination, array('solar', 'lunar'), true)) {
            $filtered_phenomena = array();
            foreach ($phenomena as $entry) {
                $entry_is_lunar = astro_atmos_optics_is_lunar_phenomenon($entry);
                if ($fallback_illumination === 'lunar' && $entry_is_lunar) {
                    $filtered_phenomena[] = $entry;
                } elseif ($fallback_illumination === 'solar' && !$entry_is_lunar) {
                    $filtered_phenomena[] = $entry;
                }
            }
            $phenomena = $filtered_phenomena;
        }
        if (isset($prediction['clouds']) && is_array($prediction['clouds'])) {
            $clouds = $prediction['clouds'];
        } elseif (isset($prediction['cloud_classification']) && is_array($prediction['cloud_classification'])) {
            $clouds = $prediction['cloud_classification'];
        }
        if (isset($request['mode']) && is_string($request['mode'])) {
            $mode = $request['mode'];
        } elseif (isset($target['mode']) && is_string($target['mode'])) {
            $mode = $target['mode'];
        }
        if (isset($request['prediction_time']) && is_string($request['prediction_time'])) {
            $prediction_time = $request['prediction_time'];
        }
        if (isset($request['options']) && is_array($request['options']) && isset($request['options']['illumination']) && is_string($request['options']['illumination'])) {
            $illumination = $request['options']['illumination'];
        } elseif (isset($target['illumination']) && is_string($target['illumination'])) {
            $illumination = $target['illumination'];
        }
        if (strpos($illumination, ',') !== false && in_array($fallback_illumination, array('solar', 'lunar'), true)) {
            $illumination = $fallback_illumination;
        }
        if (isset($target['name']) && is_string($target['name'])) {
            $target_name = $target['name'];
        }
        if (isset($request['location']) && is_array($request['location'])) {
            if (isset($request['location']['lat']) && is_numeric($request['location']['lat'])) {
                $latitude = (float) $request['location']['lat'];
            }
            if (isset($request['location']['lon']) && is_numeric($request['location']['lon'])) {
                $longitude = (float) $request['location']['lon'];
            }
        }
        if (($latitude === null || $longitude === null) && isset($target['location']) && is_array($target['location'])) {
            if ($latitude === null && isset($target['location']['lat']) && is_numeric($target['location']['lat'])) {
                $latitude = (float) $target['location']['lat'];
            }
            if ($longitude === null && isset($target['location']['lon']) && is_numeric($target['location']['lon'])) {
                $longitude = (float) $target['location']['lon'];
            }
        }
        if ($illumination === 'solar' && isset($celestial['sun']) && is_array($celestial['sun'])) {
            $active_body_label = 'Sun';
            if (isset($celestial['sun']['altitude']) && is_numeric($celestial['sun']['altitude'])) {
                $active_body_altitude = (float) $celestial['sun']['altitude'];
            }
        } elseif ($illumination === 'lunar' && isset($celestial['moon']) && is_array($celestial['moon'])) {
            $active_body_label = 'Moon';
            if (isset($celestial['moon']['altitude']) && is_numeric($celestial['moon']['altitude'])) {
                $active_body_altitude = (float) $celestial['moon']['altitude'];
            }
        } elseif (isset($celestial['sun']) && is_array($celestial['sun']) && isset($celestial['sun']['altitude']) && is_numeric($celestial['sun']['altitude'])) {
            $active_body_label = 'Sun';
            $active_body_altitude = (float) $celestial['sun']['altitude'];
        } elseif (isset($celestial['moon']) && is_array($celestial['moon']) && isset($celestial['moon']['altitude']) && is_numeric($celestial['moon']['altitude'])) {
            $active_body_label = 'Moon';
            $active_body_altitude = (float) $celestial['moon']['altitude'];
        }
        if (isset($celestial['sun']) && is_array($celestial['sun']) && isset($celestial['sun']['altitude']) && is_numeric($celestial['sun']['altitude'])) {
            $celestial_details[] = array(
                'label' => 'Sun',
                'altitude' => (float) $celestial['sun']['altitude'],
            );
        }
        if (isset($celestial['moon']) && is_array($celestial['moon']) && isset($celestial['moon']['altitude']) && is_numeric($celestial['moon']['altitude'])) {
            $celestial_details[] = array(
                'label' => 'Moon',
                'altitude' => (float) $celestial['moon']['altitude'],
            );
        }

        return array(
            'request' => $request,
            'target' => $target,
            'sources' => $sources,
            'phenomena' => $phenomena,
            'clouds' => $clouds,
            'mode' => $mode,
            'illumination' => $illumination,
            'prediction_time' => $prediction_time,
            'target_name' => $target_name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'active_body_label' => $active_body_label,
            'active_body_altitude' => $active_body_altitude,
            'celestial_details' => $celestial_details,
        );
    }
}

if (!function_exists('astro_atmos_optics_loaded_datasets')) {
    function astro_atmos_optics_loaded_datasets()
    {
        $loaded_datasets = array();
        foreach (astro_atmos_optics_datasets() as $dataset) {
            $payload = null;
            $error_message = '';
            if (is_file($dataset['json_path'])) {
                $json = file_get_contents($dataset['json_path']);
                if ($json === false) {
                    $error_message = 'The atmospheric optics JSON file could not be read.';
                } else {
                    $payload = json_decode($json, true);
                    if (!is_array($payload)) {
                        $payload = null;
                        $error_message = 'The atmospheric optics JSON file is not valid JSON.';
                    }
                }
            } else {
                $error_message = 'The atmospheric optics JSON file does not exist yet.';
            }

            $loaded_datasets[] = array_merge(
                $dataset,
                array(
                    'payload' => $payload,
                    'error_message' => $error_message,
                    'normalized' => is_array($payload) ? astro_atmos_optics_normalize_payload($payload, $dataset['fallback_illumination']) : array(),
                )
            );
        }

        return $loaded_datasets;
    }
}

if (!function_exists('astro_atmos_optics_table_options')) {
    function astro_atmos_optics_table_options()
    {
        return array(
            'table_class' => 'atmos-optics-table',
            'table_style' => array(
                'font-size' => 'clamp(0.74rem, 0.68rem + 0.35vw, 0.94rem)',
                'overflow' => 'visible !important',
            ),
            'wrapper_class' => 'atmos-optics-table-wrap',
            'wrapper_style' => 'overflow: visible;',
        );
    }
}

if (!function_exists('astro_atmos_optics_cell_style')) {
    function astro_atmos_optics_cell_style($extra = array())
    {
        return array_merge(
            array(
                'padding-top' => '1.25px !important',
                'padding-bottom' => '1.25px !important',
                'vertical-align' => 'middle',
            ),
            $extra
        );
    }
}

if (!function_exists('astro_atmos_optics_timeline_cell_style')) {
    function astro_atmos_optics_timeline_cell_style()
    {
        return astro_atmos_optics_cell_style(array(
            'text-align' => 'center !important',
            'white-space' => 'nowrap',
        ));
    }
}

if (!function_exists('astro_atmos_optics_current_cell_style')) {
    function astro_atmos_optics_current_cell_style()
    {
        return astro_atmos_optics_cell_style(array(
            'position' => 'relative',
            'overflow' => 'visible',
        ));
    }
}

if (!function_exists('astro_atmos_optics_cell_options')) {
    function astro_atmos_optics_cell_options($sort_value = null, $style = null, $class = '')
    {
        $options = array(
            'style' => $style === null ? astro_atmos_optics_cell_style() : $style,
        );
        if ($class !== '') {
            $options['class'] = $class;
        }
        if ($sort_value !== null) {
            $options['sort_value'] = $sort_value;
        }

        return $options;
    }
}

if (!function_exists('astro_atmos_optics_header_cell')) {
    function astro_atmos_optics_header_cell($text, $style = null, $class = '')
    {
        return astro_table_text_cell($text, astro_atmos_optics_cell_options(null, $style, $class));
    }
}

if (!function_exists('astro_atmos_optics_text_cell')) {
    function astro_atmos_optics_text_cell($text, $sort_value = null, $style = null, $class = '')
    {
        return astro_table_text_cell($text, astro_atmos_optics_cell_options($sort_value, $style, $class));
    }
}

if (!function_exists('astro_atmos_optics_html_cell')) {
    function astro_atmos_optics_html_cell($html, $sort_value = null, $style = null, $class = '')
    {
        return astro_table_html_cell($html, astro_atmos_optics_cell_options($sort_value, $style, $class));
    }
}

if (!function_exists('astro_atmos_optics_timeline_labels')) {
    function astro_atmos_optics_timeline_labels($phenomena)
    {
        $timeline_labels = array();
        foreach ($phenomena as $timeline_entry_source) {
            if (!is_array($timeline_entry_source) || !isset($timeline_entry_source['timeline']) || !is_array($timeline_entry_source['timeline'])) {
                continue;
            }
            foreach ($timeline_entry_source['timeline'] as $timeline_entry) {
                if (!is_array($timeline_entry)) {
                    continue;
                }
                if (!isset($timeline_entry['label']) || !is_string($timeline_entry['label'])) {
                    continue;
                }
                if ($timeline_entry['label'] === 'now') {
                    continue;
                }
                if (!isset($timeline_entry['probability']) || !is_numeric($timeline_entry['probability'])) {
                    continue;
                }
                if (!array_key_exists($timeline_entry['label'], $timeline_labels)) {
                    $timeline_labels[$timeline_entry['label']] = $timeline_entry['label'];
                }
            }
        }

        return $timeline_labels;
    }
}

if (!function_exists('astro_atmos_optics_render_phenomena_table')) {
    function astro_atmos_optics_render_phenomena_table($normalized)
    {
        $timeline_labels = astro_atmos_optics_timeline_labels($normalized['phenomena']);
        $timeline_style = astro_atmos_optics_timeline_cell_style();

        $headers = array(
            astro_atmos_optics_header_cell('Illumination'),
            astro_atmos_optics_header_cell('Phenomenon'),
            astro_atmos_optics_header_cell('Current', $timeline_style),
        );
        foreach ($timeline_labels as $timeline_label) {
            $headers[] = astro_atmos_optics_header_cell($timeline_label, $timeline_style, 'atmos-optics-timeline-heading');
        }

        $rows = array();
        foreach ($normalized['phenomena'] as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $label = '';
            $current_probability = null;
            $timeline_values = array();
            $reason = '';
            $illumination_label = astro_atmos_optics_illumination_label_for_entry($entry);
            if (isset($entry['label']) && is_string($entry['label'])) {
                $label = $entry['label'];
            } elseif (isset($entry['id']) && is_string($entry['id'])) {
                $label = ucwords(str_replace(array('_', '-'), ' ', $entry['id']));
            }
            if (isset($entry['current']) && is_array($entry['current'])) {
                if (isset($entry['current']['probability']) && is_numeric($entry['current']['probability'])) {
                    $current_probability = (float) $entry['current']['probability'];
                }
                if (isset($entry['current']['reason']) && is_string($entry['current']['reason'])) {
                    $reason = $entry['current']['reason'];
                }
            }
            if (isset($entry['timeline']) && is_array($entry['timeline'])) {
                foreach ($entry['timeline'] as $timeline_entry) {
                    if (!is_array($timeline_entry)) {
                        continue;
                    }
                    if (!isset($timeline_entry['label']) || !is_string($timeline_entry['label'])) {
                        continue;
                    }
                    if ($timeline_entry['label'] === 'now') {
                        continue;
                    }
                    if (!isset($timeline_entry['probability']) || !is_numeric($timeline_entry['probability'])) {
                        continue;
                    }
                    $timeline_values[$timeline_entry['label']] = $timeline_entry['probability'];
                }
            }

            $row = array(
                astro_atmos_optics_text_cell($illumination_label, $illumination_label),
                astro_atmos_optics_text_cell($label, $label),
                astro_atmos_optics_html_cell(astro_atmos_optics_current_probability($current_probability, $reason), $current_probability, astro_atmos_optics_current_cell_style(), 'atmos-optics-current-column'),
            );
            foreach ($timeline_labels as $timeline_label) {
                $row[] = array_key_exists($timeline_label, $timeline_values)
                    ? astro_atmos_optics_html_cell(astro_atmos_optics_probability_chip($timeline_values[$timeline_label]), $timeline_values[$timeline_label], $timeline_style, 'atmos-optics-timeline-cell')
                    : astro_atmos_optics_html_cell('', null, $timeline_style, 'atmos-optics-timeline-cell');
            }
            $rows[] = $row;
        }

        render_sortable_table($headers, $rows, array(), astro_atmos_optics_table_options());
    }
}

if (!function_exists('astro_atmos_optics_render_cloud_table')) {
    function astro_atmos_optics_render_cloud_table($cloud_layers)
    {
        $timeline_style = astro_atmos_optics_timeline_cell_style();
        $headers = array(
            astro_atmos_optics_header_cell('Layer'),
            astro_atmos_optics_header_cell('WMO'),
            astro_atmos_optics_header_cell('Altitude'),
            astro_atmos_optics_header_cell('Coverage'),
            astro_atmos_optics_header_cell('Base'),
            astro_atmos_optics_header_cell('Top'),
            astro_atmos_optics_header_cell('Confidence', $timeline_style),
        );

        $rows = array();
        foreach ($cloud_layers as $cloud_layer) {
            if (!is_array($cloud_layer)) {
                continue;
            }

            $row_number = count($rows) + 1;
            $layer_id = isset($cloud_layer['layer_id']) ? $cloud_layer['layer_id'] : $row_number;
            $genus = isset($cloud_layer['wmo_genus']) && is_string($cloud_layer['wmo_genus']) ? $cloud_layer['wmo_genus'] : '';
            $code = isset($cloud_layer['wmo_code']) && is_string($cloud_layer['wmo_code']) ? $cloud_layer['wmo_code'] : '';
            $altitude_category = isset($cloud_layer['altitude_category']) && is_string($cloud_layer['altitude_category']) ? $cloud_layer['altitude_category'] : '';
            $coverage = isset($cloud_layer['coverage']) && is_string($cloud_layer['coverage']) ? $cloud_layer['coverage'] : '';
            $cloud_base = isset($cloud_layer['cloud_base_m']) ? $cloud_layer['cloud_base_m'] : null;
            $cloud_top = isset($cloud_layer['cloud_top_m']) ? $cloud_layer['cloud_top_m'] : null;
            $confidence = isset($cloud_layer['confidence']) ? $cloud_layer['confidence'] : null;
            $wmo_label = trim($genus . ($code !== '' ? ' (' . $code . ')' : ''));

            $rows[] = array(
                astro_atmos_optics_text_cell((string) $layer_id, $layer_id),
                astro_atmos_optics_text_cell($wmo_label, $wmo_label),
                astro_atmos_optics_text_cell(ucfirst($altitude_category), $altitude_category),
                astro_atmos_optics_text_cell(ucfirst($coverage), $coverage),
                astro_atmos_optics_text_cell(astro_atmos_optics_format_meters($cloud_base), is_numeric($cloud_base) ? (float) $cloud_base : ''),
                astro_atmos_optics_text_cell(astro_atmos_optics_format_meters($cloud_top), is_numeric($cloud_top) ? (float) $cloud_top : ''),
                astro_atmos_optics_html_cell(astro_atmos_optics_probability_chip($confidence), is_numeric($confidence) ? (float) $confidence : '', $timeline_style),
            );
        }

        render_sortable_table($headers, $rows, array(), astro_atmos_optics_table_options());
    }
}

if (!function_exists('astro_atmos_optics_dataset_details')) {
    function astro_atmos_optics_dataset_details($normalized)
    {
        $details = array();
        if ($normalized['target_name'] !== '') {
            $details[] = 'Target: ' . htmlspecialchars($normalized['target_name'], ENT_QUOTES, 'UTF-8');
        }
        if ($normalized['mode'] !== '') {
            $details[] = 'Mode: ' . htmlspecialchars(ucfirst($normalized['mode']), ENT_QUOTES, 'UTF-8');
        }
        if ($normalized['prediction_time'] !== '') {
            $details[] = 'Prediction time: ' . htmlspecialchars(astro_atmos_optics_format_timestamp($normalized['prediction_time']), ENT_QUOTES, 'UTF-8');
        }
        if ($normalized['celestial_details'] !== array()) {
            $celestial_parts = array();
            foreach ($normalized['celestial_details'] as $celestial_detail) {
                if (!is_array($celestial_detail) || !isset($celestial_detail['label']) || !isset($celestial_detail['altitude'])) {
                    continue;
                }
                $celestial_parts[] = htmlspecialchars((string) $celestial_detail['label'], ENT_QUOTES, 'UTF-8') .
                    ': ' .
                    htmlspecialchars(astro_atmos_optics_format_altitude($celestial_detail['altitude']), ENT_QUOTES, 'UTF-8');
            }
            if ($celestial_parts !== array()) {
                $details[] = implode(' | ', $celestial_parts);
            }
        } elseif ($normalized['active_body_label'] !== '' && $normalized['active_body_altitude'] !== null) {
            $details[] = htmlspecialchars($normalized['active_body_label'], ENT_QUOTES, 'UTF-8') .
                ': ' .
                htmlspecialchars(astro_atmos_optics_format_altitude($normalized['active_body_altitude']), ENT_QUOTES, 'UTF-8');
        }

        return $details;
    }
}

if (!function_exists('astro_atmos_optics_sources_text')) {
    function astro_atmos_optics_sources_text($sources)
    {
        $source_parts = array();
        foreach ($sources as $source) {
            if (!is_array($source)) {
                continue;
            }
            $source_label = '';
            if (isset($source['label']) && is_string($source['label'])) {
                $source_label = $source['label'];
            } elseif (isset($source['id']) && is_string($source['id'])) {
                $source_label = ucwords(str_replace(array('_', '-'), ' ', $source['id']));
            }
            $source_timestamp = '';
            if (isset($source['timestamp']) && is_string($source['timestamp'])) {
                $source_timestamp = $source['timestamp'];
            }
            $source_parts[] = trim($source_label . ' ' . $source_timestamp);
        }

        return implode(', ', $source_parts);
    }
}

if (!function_exists('astro_atmos_optics_dataset_html')) {
    function astro_atmos_optics_dataset_html($dataset)
    {
        ob_start();

        $payload = $dataset['payload'];
        if (!is_array($payload)) {
            echo '<p>', htmlspecialchars($dataset['error_message'], ENT_QUOTES, 'UTF-8'), '</p>';
            return ob_get_clean();
        }

        $normalized = $dataset['normalized'];
        $details = astro_atmos_optics_dataset_details($normalized);
        $maps_href = '';
        if ($normalized['latitude'] !== null && $normalized['longitude'] !== null) {
            $maps_href = 'https://maps.google.com/maps?q=' . $normalized['latitude'] . ',' . $normalized['longitude'];
        }

        echo '<p class="weather-card__meta" style="margin:0 0 2px 0;">', implode(' | ', $details), '</p>';
        echo '<p class="weather-card__meta" style="margin:0 0 5px 0;">';
        if ($maps_href !== '') {
            echo '<a href="', htmlspecialchars($maps_href, ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener noreferrer">',
                htmlspecialchars(number_format((float) $normalized['latitude'], 2) . ', ' . number_format((float) $normalized['longitude'], 2), ENT_QUOTES, 'UTF-8'),
                '</a>';
            echo '<span class="weather-card__dot" aria-hidden="true">&bull;</span>';
        }
        echo '<a href="', htmlspecialchars($dataset['json_href'], ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener noreferrer">Raw JSON</a>';
        echo '</p>';

        if ($normalized['phenomena'] === array()) {
            echo '<p>No phenomena were included in this payload.</p>';
        } else {
            astro_atmos_optics_render_phenomena_table($normalized);
        }

        $cloud_layers = array();
        if (isset($normalized['clouds']['cloud_layers']) && is_array($normalized['clouds']['cloud_layers'])) {
            $cloud_layers = $normalized['clouds']['cloud_layers'];
        }
        if (!empty($dataset['show_cloud_classification']) && $cloud_layers !== array()) {
            echo '<h2 class="panel-title" style="margin:12px 0 4px 0;">Cloud Classification</h2>';
            astro_atmos_optics_render_cloud_table($cloud_layers);
        }

        if ($normalized['sources'] !== array()) {
            echo '<p class="page-note" style="margin-top:10px;">Sources: ',
                htmlspecialchars(astro_atmos_optics_sources_text($normalized['sources']), ENT_QUOTES, 'UTF-8'),
                '</p>';
        }

        return ob_get_clean();
    }
}

if (!function_exists('astro_atmos_optics_tooltip_script')) {
    function astro_atmos_optics_tooltip_script()
    {
        return <<<'HTML'
<script>
(() => {
  const tooltip = document.createElement('div');
  tooltip.className = 'atmos-optics-pointer-tooltip';
  tooltip.setAttribute('role', 'tooltip');
  document.body.appendChild(tooltip);

  function moveAtmosOpticsTooltip(cell, pointerEvent) {
    const trigger = cell.querySelector('.atmos-optics-current-trigger');
    const reason = cell.dataset.atmosOpticsReason || (trigger ? trigger.getAttribute('aria-label') : '');
    if (!trigger || !reason) {
      return;
    }

    const padding = 8;
    const pointerGap = 12;
    const anchor = pointerEvent && typeof pointerEvent.clientX === 'number'
      ? { x: pointerEvent.clientX, y: pointerEvent.clientY }
      : (() => {
          const rect = trigger.getBoundingClientRect();
          return { x: rect.right, y: rect.top + rect.height / 2 };
        })();
    tooltip.textContent = reason;
    tooltip.classList.add('is-visible');
    tooltip.style.maxWidth = '';
    const availableWidth = Math.max(96, window.innerWidth - anchor.x - pointerGap - padding);
    tooltip.style.maxWidth = `${availableWidth}px`;
    const tooltipRect = tooltip.getBoundingClientRect();
    let left = anchor.x + pointerGap;
    let top = anchor.y + padding;

    top = Math.max(padding, Math.min(top, window.innerHeight - tooltipRect.height - padding));

    tooltip.style.setProperty('--atmos-tooltip-left', `${Math.round(left)}px`);
    tooltip.style.setProperty('--atmos-tooltip-top', `${Math.round(top)}px`);
  }

  function hideAtmosOpticsTooltip() {
    tooltip.classList.remove('is-visible');
  }

  document.querySelectorAll('.atmos-optics-current-cell').forEach((cell) => {
    cell.addEventListener('pointerenter', (event) => moveAtmosOpticsTooltip(cell, event));
    cell.addEventListener('pointermove', (event) => moveAtmosOpticsTooltip(cell, event));
    cell.addEventListener('focusin', () => moveAtmosOpticsTooltip(cell, null));
    cell.addEventListener('pointerleave', hideAtmosOpticsTooltip);
    cell.addEventListener('focusout', hideAtmosOpticsTooltip);
  });
})();
</script>
HTML;
    }
}

if (!function_exists('astro_atmos_optics_module_payload')) {
    function astro_atmos_optics_module_payload($request = array())
    {
        $sections = array();
        foreach (astro_atmos_optics_loaded_datasets() as $dataset) {
            $sections[] = array(
                'type' => 'html',
                'title' => $dataset['title'],
                'html' => astro_atmos_optics_dataset_html($dataset),
            );
        }

        return array(
            'title' => 'Atmospheric Optics',
            'sections' => $sections,
            'scripts' => array(astro_atmos_optics_tooltip_script()),
        );
    }
}

return static function ($request) {
    return astro_atmos_optics_module_payload($request);
};
