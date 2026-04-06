<?php

require_once __DIR__ . '/bootstrap.php';

if (!isset($scale) || !is_array($scale)) {
    $scale = array(
        'JPG' => array(
            'FullDisk' => array(
                'x' => 0,
                'y' => 0,
                'w' => 678,
                'h' => 678,
                'r' => 8,
                'rx' => 8,
                'ry' => 8,
            ),
            'CONUS' => array(
                'x' => 901,
                'y' => 419,
                'w' => 1250,
                'h' => 750,
                'r' => 2,
                'rx' => 2,
                'ry' => 2,
            ),
            'TX' => array(
                'x' => 1409,
                'y' => 836,
                'w' => 1200,
                'h' => 1200,
                'r' => 1,
                'rx' => 0.5,
                'ry' => 0.5,
            )
        ),
        'GIF' => array(
            'CONUS' => array(
                'x' => 901,
                'y' => 419,
                'w' => 625,
                'h' => 375,
                'r' => 4,
                'rx' => 4,
                'ry' => 4,
            ),
            'TX' => array(
                'x' => 1409,
                'y' => 836,
                'w' => 600,
                'h' => 600,
                'r' => 1,
                'rx' => 1,
                'ry' => 1,
            )
        ),
        'NAM' => array(
            'NAM84' => array(
                'x' => 0,
                'y' => 0,
                'w' => 1013,
                'h' => 728,
                'r' => 1,
                'rx' => 1,
                'ry' => 1,
            ),
            'NAM240' => array(
                'x' => 0,
                'y' => 0,
                'w' => 1024,
                'h' => 678,
                'r' => 1,
                'rx' => 1,
                'ry' => 1,
            ),
            'NAM384' => array(
                'x' => 0,
                'y' => 0,
                'w' => 800,
                'h' => 600,
                'r' => 1,
                'rx' => 1,
                'ry' => 1,
            ),
        ),
        'LP' => array(
            'TX' => array(
                'x' => 0,
                'y' => 0,
                'w' => 2400,
                'h' => 1400,
                'r' => 1,
                'rx' => 1,
                'ry' => 1,
            ),
        )
    );
}

if (!function_exists('plotmarker')) {
    function plotmarker($x, $y, $it = 'JPG', $rg = 'FullDisk', $type = 'line1')
    {
        global $scale;
        $x = round(($x - $scale[$it][$rg]['x']) / $scale[$it][$rg]['rx']);
        $y = round(($y - $scale[$it][$rg]['y']) / $scale[$it][$rg]['ry']);
        if ($x > 0 && $x <= $scale[$it][$rg]['w'] && $y > 0 && $y <= $scale[$it][$rg]['h']) {
            $lenbar = ceil(3 / $scale[$it][$rg]['r']);
            echo "<line x1='", $x - $lenbar, "' y1='", $y, "' x2='", $x + $lenbar, "' y2='", $y, "' class='", $type, "' />";
            echo "<line x1='", $x, "' y1='", $y - $lenbar, "' x2='", $x, "' y2='", $y + $lenbar, "' class='", $type, "' />";
        }
    }
}

if (!function_exists('plotmarker_tooltip')) {
    function plotmarker_tooltip($title, $value = '')
    {
        $parts = array();
        $title = trim((string) $title);
        $value = trim((string) $value);

        if ($title !== '') {
            $parts[] = $title;
        }
        if ($value !== '') {
            $parts[] = $value;
        }

        return implode(' | ', $parts);
    }
}

if (!function_exists('plotmarkerlabel')) {
    function plotmarkerlabel($x, $y, $it = 'JPG', $rg = 'FullDisk', $title, $iddiv, $value = '')
    {
        global $scale;
        $x = ($x - $scale[$it][$rg]['x']) / $scale[$it][$rg]['rx'];
        $y = ($y - $scale[$it][$rg]['y']) / $scale[$it][$rg]['ry'];
        if ($x > 0 && $x <= $scale[$it][$rg]['w'] && $y > 0 && $y <= $scale[$it][$rg]['h']) {
            $lenbar = ceil(3 / $scale[$it][$rg]['r']);
            $tooltip = htmlspecialchars(plotmarker_tooltip($title, $value), ENT_QUOTES, 'UTF-8');
            echo '<rect name="', $tooltip, '" data-tooltip="', $tooltip, '" x="', $x - $lenbar, '" y="', $y - $lenbar, '" width="', $lenbar * 2, '" height="', $lenbar * 2, '" fill-opacity="0" onmousemove="showtooltip(evt, ', $x, ', ', $y, ', \'', htmlspecialchars($iddiv, ENT_QUOTES, 'UTF-8'), '\')" onmouseout="hidetooltip(\'', htmlspecialchars($iddiv, ENT_QUOTES, 'UTF-8'), '\')" />';
        }
    }
}

if (!function_exists('plotpath')) {
    function plotpath($path, $it = 'JPG', $rg = 'FullDisk', $type = 'line1')
    {
        global $scale;
        $d = '';
        $i = 0;
        foreach ($path as $p) {
            $x = $p[1];
            $y = $p[2];
            $x = round(($x - $scale[$it][$rg]['x']) / $scale[$it][$rg]['rx']);
            $y = round(($y - $scale[$it][$rg]['y']) / $scale[$it][$rg]['ry']);
            if ($x > 0 && $x <= $scale[$it][$rg]['w'] && $y > 0 && $y <= $scale[$it][$rg]['h']) {
                if ($i == 0) {
                    $d = 'M' . $x . ' ' . $y;
                    $i = 1;
                } else {
                    $d = $d . ' L' . $x . ' ' . $y;
                }
            }
        }
        echo '<path d="', $d, '" fill-opacity="0" class="', $type, '" />';
    }
}

if (!function_exists('getimg')) {
    function getimg($it = 'JPG', $rg = 'FullDisk', $ch = '01')
    {
        $imgpre = array(
            'FullDisk' => 'https://cdn.star.nesdis.noaa.gov/GOES16/ABI/FD/',
            'CONUS' => 'https://cdn.star.nesdis.noaa.gov/GOES16/ABI/CONUS/',
            'TX' => 'https://cdn.star.nesdis.noaa.gov/GOES16/ABI/SECTOR/sp/',
        );
        $imgpost = array(
            'JPG' => array(
                'FullDisk' => '678x678.jpg',
                'CONUS' => '1250x750.jpg',
                'TX' => '1200x1200.jpg'
            ),
            'GIF' => array(
                'FullDisk' => '600x600.gif',
                'CONUS' => '625x375.gif',
                'TX' => '600x600.gif'
            ),
        );
        $rgcode = array(
            'FullDisk' => 'FD',
            'CONUS' => 'CONUS',
            'TX' => 'SP'
        );
        $imgmid = $ch . '/';
        if ($it == 'GIF') {
            $imgmid .= '/GOES16-' . $rgcode[$rg] . '-' . $ch . '-';
        }
        return $imgpre[$rg] . $imgmid . $imgpost[$it][$rg];
    }
}

if (!function_exists('plotsvg_scale_x')) {
    function plotsvg_scale_x($x, $param)
    {
        return intval($param['marginleft'] + $param['mainwidth'] / ($param['xmax'] - $param['xmin']) * ($x - $param['xmin']));
    }
}

if (!function_exists('plotsvg_scale_y')) {
    function plotsvg_scale_y($y, $param)
    {
        return intval($param['margintop'] + $param['mainheight'] - $param['mainheight'] / ($param['ymax'] - $param['ymin']) * ($y - $param['ymin']));
    }
}

if (!function_exists('plotsvg_line')) {
    function plotsvg_line($xstart, $ystart, $xend, $yend, $type = 'line1')
    {
        echo '<line x1="', $xstart, '" y1="', $ystart, '" x2="', $xend, '" y2="', $yend, '" class="', $type, '" />';
    }
}

if (!function_exists('plotsvg_text')) {
    function plotsvg_text($text, $x, $y, $type = 'sctext1', $rotate = 0)
    {
        $transform = ($rotate == 0) ? '' : 'transform=rotate(' . $rotate . ',' . ($x - 5) . ',' . $y . ')';
        echo '<text x="', $x, '" y="', $y, '" class="', $type, '" ', $transform, '>', $text, '</text>';
    }
}

if (!function_exists('plotsvg_circle')) {
    function plotsvg_circle($x, $y, $r, $type = 'sccir1', $param = array(), $tooltip = '')
    {
        echo '<circle cx="', plotsvg_scale_x($x, $param), '" cy="', plotsvg_scale_y($y, $param), '" r="', $r, '" class="', $type, '">';
        if ($tooltip !== '') {
            echo '<title>', htmlspecialchars((string) $tooltip, ENT_QUOTES, 'UTF-8'), '</title>';
        }
        echo '</circle>';
    }
}

if (!function_exists('plotsvg_rect')) {
    function plotsvg_rect($xstart, $ystart, $xend, $yend, $type = 'screct1', $param = array())
    {
        echo '<rect x="', plotsvg_scale_x($xstart, $param), '" y="', plotsvg_scale_y($yend, $param), '" width="', plotsvg_scale_x($xend, $param) - plotsvg_scale_x($xstart, $param), '" height="', plotsvg_scale_y($ystart, $param) - plotsvg_scale_y($yend, $param), '" class="', $type, '" />';
    }
}

if (!function_exists('plotsvg')) {
    function plotsvg($x, $y, $xlab = '', $param = array(), $type = '', $tooltip = array())
    {
        if ($xlab == '') {
            $xlab = $x;
        }

        $panelwidth = $param['marginleft'] + $param['marginright'] + $param['mainwidth'];
        $panelheight = $param['margintop'] + $param['marginbottom'] + $param['mainheight'];
        $len = count($x);
        $param['xmin'] = min($x);
        $param['xmax'] = max($x);

        if ($param['xbreaks'] == '') {
            $step = ceil(($param['xmax'] - $param['xmin']) / 50);
            $param['xbreaks'] = range($param['xmax'], $param['xmin'], -$step);
        }
        if ($param['ybreaks'] == '') {
            $param['ybreaks'] = range($param['ymin'], $param['ymax'], ($param['ymax'] - $param['ymin']) / 5);
        }

        echo '<div style="position:relative; width:', $panelwidth, 'px; height:', $panelheight, 'px;">';
        echo '<svg style="position:absolute; top:0px; left:0px; width:', $panelwidth, 'px; height:', $panelheight, 'px;">';

        foreach ($param['xbreaks'] as $xb) {
            plotsvg_line(
                plotsvg_scale_x($xb, $param),
                plotsvg_scale_y($param['ymin'], $param),
                plotsvg_scale_x($xb, $param),
                plotsvg_scale_y($param['ymax'], $param),
                'scline1'
            );
            for ($i = 0; $i < $len; $i++) {
                if ($x[$i] == $xb) {
                    plotsvg_text($xlab[$i], plotsvg_scale_x($xb, $param), plotsvg_scale_y($param['ymin'], $param), 'sctext2', 90);
                    break;
                }
            }
        }

        foreach ($param['ybreaks'] as $yb) {
            plotsvg_line(
                plotsvg_scale_x($param['xmin'], $param),
                plotsvg_scale_y($yb, $param),
                plotsvg_scale_x($param['xmax'], $param),
                plotsvg_scale_y($yb, $param),
                'scline1'
            );
            plotsvg_text($yb, plotsvg_scale_x(0, $param) - 10, plotsvg_scale_y($yb, $param) + 5, 'sctext1');
            plotsvg_text($yb, plotsvg_scale_x($param['xmax'], $param) + 10, plotsvg_scale_y($yb, $param) + 5, 'sctext2');
        }

        for ($i = 0; $i < $len; $i++) {
            $point_type = ($type == '') ? 'sccir1' : $type[$i];
            $point_tooltip = $tooltip[$i] ?? '';
            plotsvg_circle($x[$i], $y[$i], 2, $point_type, $param, $point_tooltip);
        }

        echo '</svg>';
        echo '</div>';
    }
}
