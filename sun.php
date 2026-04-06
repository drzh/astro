<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
<?php include 'menu.php'; ?>
<?php
$fi = 'sun/AIAsynoptic0304.full.txt';
$intensity_max = null;
if (file_exists($fi)) {
    $lines = file($fi);
    foreach ($lines as $line) {
        if (strpos($line, 'intensity_max') !== false) {
            $parts = preg_split('/\s+/', trim($line));
            if (count($parts) >= 2) {
                $intensity_max = intval($parts[1]);
            }
            break;
        }
    }
}

$imgurl = [
    'AIA 304 | <a href="/sun/AIAsynoptic0304.full.txt">Max: ' . $intensity_max . '</a> | <a href="https://www.swpc.noaa.gov/products/goes-solar-ultraviolet-imager-suvi" target="_blank" rel="noopener noreferrer">GOES SUVI</a>' => ['sun/AIAsynoptic0304.full.png', 'sun/AIAsynoptic0304.full.png', 256, 256],
    'GOES-19 SUVI - AIA 304 &Aring / 50,000 K / Transition region / Chromosphere' => ['https://services.swpc.noaa.gov/images/animations/suvi/primary/304/latest.png', 'https://services.swpc.noaa.gov/images/animations/suvi/primary/304/latest.png', 256, 256],
    'HMI Continuum' => ['https://soho.nascom.nasa.gov/data/realtime/hmi_igr/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/hmi_igr/1024/latest.jpg', 256, 256],
    'GOES19 CCOR-1 | <a href="https://www.swpc.noaa.gov/products/ccor-1-coronagraph-experimental" target="_blank" rel="noopener noreferrer">Animation</a>' => ['https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg', 'https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg', 256, 256],
    'LASCO C2 | <a href="https://www.swpc.noaa.gov/products/lasco-coronagraph" target="_blank" rel="noopener noreferrer">Animation</a>' => ['https://soho.nascom.nasa.gov/data/realtime/c2/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c2/1024/latest.jpg', 256, 256],
    'LASCO C3 | <a href="https://www.swpc.noaa.gov/products/lasco-coronagraph" target="_blank" rel="noopener noreferrer">Animation</a>' => ['https://soho.nascom.nasa.gov/data/realtime/c3/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c3/1024/latest.jpg', 256, 256],
    'SDO Farside Image' => ['http://jsoc.stanford.edu/data/farside/Recent/Composite_Map.png', 'http://jsoc.stanford.edu/data/farside/Recent/Composite_Map.png', 450, 175],
    'Solar Cycle Sunspot Number Progression' => ['https://helioforecast.space//static/sync/icme_solar_cycle/cycle25_prediction_focus.png', 'https://helioforecast.space//static/sync/icme_solar_cycle/cycle25_prediction_focus.png', 450, 225],
];

$ran = rand(1, 1000);
$display_width = 512;

echo '<div class="weather-stack">';
foreach ($imgurl as $title => $url) {
    echo '<section class="panel">';
    echo '<h2 class="panel-title">', $title, '</h2>';
    echo '<figure class="media-panel media-panel--compact-graphic image-scroll">';
    echo '<a href="', $url[1], '?=', $ran, '" target="_blank" rel="noopener noreferrer"><img class="media-panel__image--scaled" style="--media-max-width:', $display_width, 'px;" width="', $url[2], '" height="', $url[3], '" src="', $url[0], '?=', $ran, '" alt="', htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async"></a>';
    echo '</figure>';
    echo '</section>';
}
echo '</div>';
?>
<?php include 'tail.php'; ?>
</body>
</html>
