<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
<?php include 'menu.php'; ?>
<?php require_once __DIR__ . '/includes/table.php'; ?>
<?php
$imgurl = [
    'SWPC Real-Time Solar Wind' => ['img', 'https://services.swpc.noaa.gov/images/geospace/geospace_1_day.png', 'https://www.swpc.noaa.gov/products/real-time-solar-wind', 640, 450],
    'NOAA Aurora Forecast - 3 Days' => ['table', 'sun/aurora.3day.tsv', '', 0, 0],
    'GFZ Aurora Forecast - 3 Days' => ['img', 'https://spaceweather.gfz.de/fileadmin/SW-Monitor/kp_swift_ensemble_LAST.png', 'https://spaceweather.gfz.de/fileadmin/SW-Monitor/kp_swift_ensemble_LAST.png', 660, 352],
    'Aurora Forecast - Tonight' => ['img', 'https://services.swpc.noaa.gov/experimental/images/aurora_dashboard/tonights_static_viewline_forecast.png', 'https://services.swpc.noaa.gov/experimental/images/aurora_dashboard/tonights_static_viewline_forecast.png', 512, 512],
    'Aurora Forecast - 30 mins' => ['img', 'https://services.swpc.noaa.gov/images/animations/ovation/north/latest.jpg', 'https://services.swpc.noaa.gov/images/animations/ovation/north/latest.jpg', 512, 512],
    'GOES19 CCOR-1 | <a href="https://www.swpc.noaa.gov/products/ccor-1-coronagraph-experimental" target="_blank" rel="noopener noreferrer">Video</a>' => ['img', 'https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg', 'https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg', 512, 512],
    'LASCO C2' => ['img', 'https://soho.nascom.nasa.gov/data/realtime/c2/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c2/1024/latest.jpg', 512, 512],
    'LASCO C3' => ['img', 'https://soho.nascom.nasa.gov/data/realtime/c3/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c3/1024/latest.jpg', 512, 512],
    'Geomagnetic Latitude' => ['table', 'sun/site.geomag.pos.lat.tsv', '', 0, 0],
];

$ran = rand(1, 1000);

echo '<div class="weather-stack">';
foreach ($imgurl as $title => $url) {
    echo '<section class="panel">';
    echo '<h2 class="panel-title">', $title, '</h2>';
    if ($url[0] === 'table') {
        display_table_from_tsv($url[1], 1);
    } else {
        echo '<figure class="media-panel media-panel--compact-graphic image-scroll">';
        echo '<a href="', $url[2], '?=', $ran, '" target="_blank" rel="noopener noreferrer"><img class="media-panel__image--scaled" style="--media-max-width:', (int) $url[3], 'px;" width="', $url[3], '" height="', $url[4], '" src="', $url[1], '?=', $ran, '" alt="', htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async"></a>';
        echo '</figure>';
    }
    echo '</section>';
}
echo '</div>';
?>
<?php include 'tail.php'; ?>
</body>
</html>
