<?php
$tz = 'America/Chicago';
$events = array('sun', 'civil', 'nautical', 'astronomical', 'moon', 'moon_phase');
$today = date('Ymd');
$days = array(
    $today,
    date('Ymd', strtotime('+1 day', strtotime($today))),
    date('Ymd', strtotime('+2 day', strtotime($today))),
    date('Ymd', strtotime('+3 day', strtotime($today))),
    date('Ymd', strtotime('+4 day', strtotime($today))),
    date('Ymd', strtotime('+5 day', strtotime($today))),
    date('Ymd', strtotime('+6 day', strtotime($today))),
);

function daynight_format_value($day, $raw, $tz)
{
    $raw = trim((string) $raw);
    if ($raw === '' || $raw === '-') {
        return '-';
    }

    if (strlen($raw) === 4 && ctype_digit($raw)) {
        return date('H:i', strtotime($day . $raw . ' ' . $tz));
    }

    return $raw;
}

function load_daynight_values($fname, $days, $tz)
{
    $values = array();
    foreach ($days as $day) {
        $values[$day] = array('begin' => '-', 'end' => '-');
    }

    if (!file_exists($fname)) {
        return $values;
    }

    $wanted = array_flip($days);
    $fh = fopen($fname, 'r');
    if (!$fh) {
        return $values;
    }

    while (($line = fgets($fh)) !== false) {
        $e = explode("\t", rtrim($line));
        if (count($e) < 3 || !isset($wanted[$e[0]])) {
            continue;
        }

        $values[$e[0]] = array(
            'begin' => daynight_format_value($e[0], $e[1], $tz),
            'end' => daynight_format_value($e[0], $e[2], $tz),
        );
    }
    fclose($fh);

    return $values;
}

function daynight_header_cell($label, $colspan = 1, $rowspan = 1)
{
    $extra_class = '';
    if (func_num_args() > 3) {
        $extra_class = (string) func_get_arg(3);
    }

    $attr = '';
    if ($colspan > 1) {
        $attr .= ' colspan="' . (int) $colspan . '"';
    }
    if ($rowspan > 1) {
        $attr .= ' rowspan="' . (int) $rowspan . '"';
    }
    if ($extra_class !== '') {
        $attr .= ' class="' . htmlspecialchars($extra_class, ENT_QUOTES, 'UTF-8') . '"';
    }

    return '<th' . $attr . '><span class="table-head-cell">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span></th>';
}

function daynight_band_class($day_index)
{
    return ($day_index % 2 === 0) ? 'daynight-band-a' : 'daynight-band-b';
}

echo '<div class="weather-stack">';
foreach ($pos as $p) {
    $siteName = $p['name'];
    $latitude = $p['latitude'];
    $longitude = $p['longitude'];
    $siteLink = $p['clear_dark_sky_link'];
    $slug = $p['slug'];
    $siteState = $p['state'];

    $coord_link = 'https://maps.google.com/maps?q=' . $latitude . ',' . $longitude;
    $has_data = count(glob('data/' . $slug . '.*')) > 0;

    echo '<section class="panel">';
    echo '<div class="weather-card__header weather-card__header--compact">';
    echo '<h2 class="weather-card__title weather-card__title--compact"><a href="', htmlspecialchars($siteLink, ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener noreferrer">', htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'), '</a></h2>';
    echo '<div class="weather-card__meta weather-card__meta--compact">';
    if (!empty($siteState)) {
        echo '<span class="weather-card__badge">', htmlspecialchars($siteState, ENT_QUOTES, 'UTF-8'), '</span>';
        echo '<span class="weather-card__dot" aria-hidden="true">&bull;</span>';
    }
    echo '<a href="', htmlspecialchars($coord_link, ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener noreferrer">', htmlspecialchars($latitude . ', ' . $longitude, ENT_QUOTES, 'UTF-8'), '</a>';
    echo '</div>';
    echo '</div>';

    if (!$has_data) {
        echo '<p class="page-note">No day/night timing files were found for this site.</p>';
        echo '</section>';
        continue;
    }

    echo '<div class="table-wrap">';
    echo '<table class="table1 daynight-table">';
    echo '<thead>';
    echo '<tr>';
    echo daynight_header_cell('Event', 1, 2, 'daynight-event-head');
    foreach ($days as $day_index => $day) {
        echo daynight_header_cell(date('D, n/j', strtotime($day)), 2, 1, daynight_band_class($day_index));
    }
    echo '</tr>';
    echo '<tr>';
    foreach ($days as $day_index => $day) {
        $band_class = daynight_band_class($day_index);
        echo daynight_header_cell('Begin', 1, 1, $band_class);
        echo daynight_header_cell('End', 1, 1, $band_class);
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ($events as $event_name) {
        $fname = 'data/' . $slug . '.' . $event_name . '.2024_2035.CDT.format';
        if (!file_exists($fname)) {
            continue;
        }

        $label = ucwords(str_replace('_', ' ', $event_name));
        $values = load_daynight_values($fname, $days, $tz);
        echo '<tr>';
        echo '<td class="daynight-event-cell">', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'), '</td>';
        foreach ($days as $day_index => $day) {
            $band_class = daynight_band_class($day_index);
            $begin = $values[$day]['begin'];
            $end = $values[$day]['end'];
            echo '<td class="', $band_class, '">', htmlspecialchars($begin, ENT_QUOTES, 'UTF-8'), '</td>';
            echo '<td class="', $band_class, '">', htmlspecialchars($end, ENT_QUOTES, 'UTF-8'), '</td>';
        }
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    echo '</section>';
}
echo '</div>';
?>
