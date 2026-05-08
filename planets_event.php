<?php
require_once __DIR__ . '/includes/table.php';

$today = date('Ymd');

function planet_event_compare($a, $b)
{
    if ($a[4] == $b[4]) {
        return 0;
    }

    return ($a[4] < $b[4]) ? -1 : 1;
}

function planet_event_plain_text($value)
{
    return trim(html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
}

function planet_event_label($event)
{
    if ($event === 'Rise') {
        return '&uarr;&nbsp;Rise&nbsp;&uarr;';
    }
    if ($event === 'Set') {
        return '&darr;&nbsp;Set&nbsp;&darr;';
    }

    return htmlspecialchars($event, ENT_QUOTES, 'UTF-8');
}

echo '<div class="weather-stack">';
foreach ($filepattern as $fp) {
    $rec = array();
    foreach (glob($fp[0]) as $f) {
        preg_match('/([A-Z]{3}).format/', $f, $matches, PREG_OFFSET_CAPTURE);
        $tz = $matches[1][0];
        $fh = fopen($f, 'r') or die("Cannot open file!\n");
        while (($line = fgets($fh)) !== false) {
            $e = explode("\t", $line);
            if (count($e) == 4) {
                $e[3] = rtrim($e[3]);
                $timestamp = strtotime($e[2] . $e[3] . ' ' . $tz);
                $day = date('Ymd', $timestamp);
                $daydiff = (strtotime($day) - strtotime($today)) / 86400;
                if ($daydiff >= 0 && $daydiff <= 6) {
                    $e[] = $timestamp;
                    $rec[] = $e;
                } elseif ($daydiff > 6) {
                    break;
                }
            }
        }
        fclose($fh);
    }

    usort($rec, 'planet_event_compare');

    $visflag = array();
    $vis = array();
    $nrec = count($rec);
    for ($i = 0; $i < $nrec; $i++) {
        $planet_key = planet_event_plain_text($rec[$i][0]);
        if (!array_key_exists($planet_key, $vis)) {
            $visflag[$planet_key] = 0;
            $vis[$planet_key] = 1;
        }
        if ($rec[$i][1] == 'Rise' && $visflag[$planet_key] == 0) {
            $visflag[$planet_key] = 1;
            $vis[$planet_key] = 0;
        }
        if ($rec[$i][1] == 'Set' && $visflag[$planet_key] == 0) {
            $visflag[$planet_key] = 1;
        }
    }

    for ($i = 0; $i < $nrec; $i++) {
        $planet_key = planet_event_plain_text($rec[$i][0]);
        if ($rec[$i][1] == 'Rise') {
            $vis[$planet_key] = 1;
        }
        $rec[$i][5] = $vis[$planet_key];
        if ($rec[$i][1] == 'Set') {
            $vis[$planet_key] = 0;
        }
    }

    $headers = array('Planet', 'Event', 'Time');
    $sections = array();
    foreach ($rec as $e) {
        if ($e[5] != 1) {
            continue;
        }

        $day_key = date('Ymd', $e[4]);
        $day_label = date('D, n/j', $e[4]);
        $time_label = date('H:i', $e[4]);
        if (!array_key_exists($day_key, $sections)) {
            $sections[$day_key] = array(
                'label' => $day_label,
                'rows' => array(),
            );
        }

        $sections[$day_key]['rows'][] = array(
            astro_table_html_cell($e[0]),
            astro_table_html_cell(planet_event_label($e[1])),
            astro_table_text_cell($time_label),
        );
    }

    ksort($sections);

    if (empty($sections)) {
        echo '<section class="panel">';
        echo '<div class="chip-row"><span class="page-toolbar__label">', htmlspecialchars($fp[1], ENT_QUOTES, 'UTF-8'), '</span></div>';
        echo '<p class="page-note">No events found in the next 7 days.</p>';
        echo '</section>';
        continue;
    }

    foreach ($sections as $section) {
        echo '<section class="panel daily-table-section">';
        echo '<div class="chip-row">';
        echo '<span class="page-toolbar__label">', htmlspecialchars($fp[1], ENT_QUOTES, 'UTF-8'), '</span>';
        echo '<span class="page-toolbar__label">', htmlspecialchars($section['label'], ENT_QUOTES, 'UTF-8'), '</span>';
        echo '</div>';
        render_plain_table($headers, $section['rows']);
        echo '</section>';
    }
}
echo '</div>';
?>
