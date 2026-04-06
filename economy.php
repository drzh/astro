<!DOCTYPE html>
<html>
<?php include "head.php"; ?>
<body>
<?php
require 'menu.php';
require_once __DIR__ . '/includes/plot.php';

$param = array(
    'marginleft' => 50,
    'margintop' => 20,
    'marginright' => 50,
    'marginbottom' => 90,
    'mainwidth' => 800,
    'mainheight' => 200,
    'xbreaks' => '',
    'ybreaks' => '',
    'xmin' => '',
    'xmax' => '',
    'ymin' => '',
    'ymax' => '',
);

$fs = array(
    array(
        'name' => 'economy/treasury/treasury_rate.300d.data',
        'title' => 'Treasury Rate',
        'ytype' => array(
            '3Mo' => 'sccir6',
            '1Yr' => 'sccir1',
            '2Yr' => 'sccir2',
            '5Yr' => 'sccir3',
            '10Yr' => 'sccir4',
            '30Yr' => 'sccir5',
        ),
        'cxstep' => 100,
    ),
);

echo '<div class="weather-stack">';
foreach ($fs as $f) {
    echo '<section class="panel">';
    echo '<h2 class="panel-title">', htmlspecialchars($f['title'], ENT_QUOTES, 'UTF-8'), '</h2>';

    $x = array();
    $y = array();
    $xlab = array();
    $type = array();
    $tooltip = array();
    $fh = fopen($f['name'], 'r');
    if ($fh) {
        while (!feof($fh)) {
            $e = explode("\t", trim((string) fgets($fh)));
            if (count($e) > 3) {
                $x[] = $e[0];
                $xlab[] = $e[1];
                $y[] = $e[2];
                $type[] = $f['ytype'][$e[3]];
                $tooltip[] = $e[1] . ' | ' . $e[3] . ': ' . $e[2];
            }
        }
        fclose($fh);

        $diff = max($y) - min($y);
        $mag = 1e-2;
        while ($diff / $mag > 2) {
            $mag *= 20;
        }
        $mag /= 20;
        $param['ymin'] = floor(min($y) / $mag) * $mag;
        $param['ymax'] = ceil(max($y) / $mag) * $mag;
        $param['ybreaks'] = range($param['ymin'], $param['ymax'], $mag);

        echo '<figure class="media-panel image-scroll">';
        echo '<div class="chart-stack">';
        $cx = $param['marginleft'];
        $cxstep = $f['cxstep'];
        echo '<svg style="width:', 100 + $cx + $cxstep * count($f['ytype']), 'px; height:20px">';
        foreach (array_keys($f['ytype']) as $k) {
            echo '<circle class="', $f['ytype'][$k], '" cx="', $cx, '" cy="10" r="2"></circle>';
            echo '<text class="', $f['ytype'][$k], '" x="', $cx + 10, '" y="15">', htmlspecialchars($k, ENT_QUOTES, 'UTF-8'), '</text>';
            $cx += $cxstep;
        }
        echo '</svg>';
        plotsvg($x, $y, $xlab, $param, $type, $tooltip);
        echo '</div>';
        echo '</figure>';
    } else {
        echo '<p class="page-note">Cannot open file: ', htmlspecialchars($f['name'], ENT_QUOTES, 'UTF-8'), '.</p>';
    }

    echo '</section>';
}
echo '</div>';

include 'tail.php';
?>
</body>
</html>
