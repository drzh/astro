<?php

function nam_overlay_load_rows($fname)
{
    $rows = array();
    if (!file_exists($fname)) {
        return $rows;
    }

    $fh = fopen($fname, 'r');
    if (!$fh) {
        return $rows;
    }

    while (($row = fgets($fh)) !== false) {
        $e = explode("\t", rtrim($row));
        if (count($e) > 2) {
            $rows[] = $e;
        }
    }
    fclose($fh);

    return $rows;
}

function nam_overlay_load_paths($pa, $template)
{
    $paths = array();
    if ($pa === '') {
        return $paths;
    }

    foreach (explode(',', $pa) as $path_name) {
        $fname = sprintf($template, $path_name);
        if (!file_exists($fname)) {
            continue;
        }

        $fh = fopen($fname, 'r');
        if (!$fh) {
            continue;
        }

        $grouped = array();
        while (($row = fgets($fh)) !== false) {
            $e = explode("\t", rtrim($row));
            if (count($e) < 3) {
                continue;
            }
            if (!array_key_exists($e[0], $grouped)) {
                $grouped[$e[0]] = array();
            }
            $grouped[$e[0]][] = $e;
        }
        fclose($fh);

        foreach ($grouped as $path_rows) {
            $paths[] = $path_rows;
        }
    }

    return $paths;
}

function nam_overlay_query($page, $begin, $end, $pa)
{
    $params = array(
        'bg' => (int) $begin,
        'ed' => (int) $end,
    );
    if ($pa !== '') {
        $params['pa'] = $pa;
    }

    return $page . '?' . http_build_query($params);
}

function nam_render_overlay_page($config)
{
    global $scale;

    $page = $config['page'];
    $selector_label = $config['selector_label'];
    $ranges = $config['ranges'];
    $projection_file = $config['projection_file'];
    $path_template = $config['path_template'];
    $image_type = $config['image_type'];
    $region = $config['region'];
    $frame_title_callback = $config['frame_title_callback'];
    $image_url_callback = $config['image_url_callback'];

    $pa = '';
    if (isset($_GET['pa'])) {
        $pa = trim((string) $_GET['pa']);
    }

    $begin = -1;
    $end = -1;
    if (isset($_GET['bg'])) {
        $begin = (int) $_GET['bg'];
    }
    if (isset($_GET['ed'])) {
        $end = (int) $_GET['ed'];
    }
    if ($begin > 0 && $end < 0) {
        $end = $begin;
    }
    if ($end > 0 && $begin < 0) {
        $begin = $end;
    }

    $marker = nam_overlay_load_rows($projection_file);
    $paths = nam_overlay_load_paths($pa, $path_template);
    $stylepos = 'top:0; left:0; width:' . $scale[$image_type][$region]['w'] . 'px; height:' . $scale[$image_type][$region]['h'] . 'px;';

    echo '<section class="panel">';
    echo '<div class="page-toolbar">';
    echo '<span class="page-toolbar__label">', htmlspecialchars($selector_label, ENT_QUOTES, 'UTF-8'), '</span>';
    echo '<div class="chip-row">';
    foreach ($ranges as $range) {
        $href = nam_overlay_query($page, $range['bg'], $range['ed'], $pa);
        $active = ($begin === $range['bg'] && $end === $range['ed']);
        echo astro_nav_item($href, $range['label'], $active);
    }
    echo '</div>';
    echo '</div>';
    echo '</section>';

    if ($begin < 0 || $end < 0) {
        return;
    }

    echo '<div class="weather-stack">';
    $scroll_group = ($end > $begin) ? 'nam-' . md5($page . '|' . $region . '|' . $begin . '|' . $end . '|' . $pa) : '';
    for ($i = $begin; $i <= $end; $i++) {
        $tooltip_id = $region . '_' . $i;
        $title = $frame_title_callback($i);
        $ran = rand(1, 1000000);
        $image_url = $image_url_callback($i, $ran);

        echo '<section class="panel">';
        echo '<h2 class="panel-title">', htmlspecialchars($title, ENT_QUOTES, 'UTF-8'), '</h2>';
        echo '<figure class="media-panel media-panel--tight image-scroll"';
        if ($scroll_group !== '') {
            echo ' data-scroll-sync-group="', htmlspecialchars($scroll_group, ENT_QUOTES, 'UTF-8'), '"';
        }
        echo '>';
        echo '<div class="responsive-stage-frame" style="width:', (int) $scale[$image_type][$region]['w'], 'px; height:', (int) $scale[$image_type][$region]['h'], 'px;">';
        echo '<div class="responsive-stage" data-stage-width="', (int) $scale[$image_type][$region]['w'], '" data-stage-height="', (int) $scale[$image_type][$region]['h'], '" style="position:relative; ', $stylepos, '">';
        echo '<img src="', htmlspecialchars($image_url, ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($title, ENT_QUOTES, 'UTF-8'), '">';
        echo '<svg style="position:absolute; ', $stylepos, '" onload="init(evt)">';
        foreach ($paths as $path) {
            plotpath($path, $image_type, $region, 'path1');
            plotpath($path, $image_type, $region, 'path2');
        }
        foreach ($marker as $m) {
            plotmarker($m[1], $m[2], $image_type, $region, 'line1');
            plotmarkerlabel($m[1], $m[2], $image_type, $region, $m[0], $tooltip_id);
        }
        echo '</svg>';
        echo '<span class="tooltip" id="', htmlspecialchars($tooltip_id, ENT_QUOTES, 'UTF-8'), '" style="position:absolute; visibility:hidden"></span>';
        echo '</div>';
        echo '</div>';
        echo '</figure>';
        echo '</section>';
    }
    echo '</div>';
}
?>
