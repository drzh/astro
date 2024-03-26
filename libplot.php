<?php

# Define the scale for different image types
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
            #'ry' => 8 * 660 / 678,
        ),
        'CONUS' => array(
            'x' => 901,
            'y' => 419,
            'w' => 1250,
            'h' => 750,
            'r' => 2,
            'rx' => 2,
            'ry' => 2,
            #'ry' => 2 * 725 / 750,
        ),
        'TX' => array(
            'x' => 1409,
            'y' => 836,
            'w' => 1200,
            'h' => 1200,
            'r' => 1,
            'rx' => 0.5,
            'ry' => 0.5,
            #'ry' => 1 * 583 / 600,
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
    )
);

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

function plotmarkerlabel($x, $y, $it = 'JPG', $rg = 'FullDisk', $title, $iddiv)
{
    global $scale;
    $x = ($x - $scale[$it][$rg]['x']) / $scale[$it][$rg]['rx'];
    $y = ($y - $scale[$it][$rg]['y']) / $scale[$it][$rg]['ry'];
    if ($x > 0 && $x <= $scale[$it][$rg]['w'] && $y > 0 && $y <= $scale[$it][$rg]['h']) {
        $lenbar = ceil(3 / $scale[$it][$rg]['r']);
        echo '<rect name="', $title, '" x="', $x - $lenbar, '" y="', $y - $lenbar, '" width="', $lenbar * 2, '" height="', $lenbar * 2, '" fill-opacity="0" onmousemove="showtooltip(evt, ', $x, ', ', $y, ', \'', $iddiv, '\')" onmouseout="hidetooltip(\'', $iddiv, '\')" />';
    }
}

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
            /* echo $x, ', ', $y, '<br>'; */
        }
    }
    echo '<path d="', $d, '" fill-opacity="0" class="', $type, '" />';
}

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
