<!DOCTYPE html>
<html>
<?php include("head.php"); ?>
<body>
<?php include('menu.php'); ?>
<?php include('libtable.php'); ?>
<table>
<tr style='vertical-align:top'><td>
<?php

$imgurl = [
    'ACE Real-Time Solar Wind' => ['img', 'https://services.swpc.noaa.gov/images/ace-mag-swepam-6-hour.gif', 'https://www.swpc.noaa.gov/products/real-time-solar-wind', 640, 512],
    'NOAA Aurora Forecast - 3 Days' => ['table', 'sun/aurora.3day.tsv', '', 0, 0],
    'GFZ Aurora Forecast - 3 Days' => ['img', 'https://spaceweather.gfz.de/fileadmin/SW-Monitor/kp_swift_ensemble_LAST.png', 'https://spaceweather.gfz.de/fileadmin/SW-Monitor/kp_swift_ensemble_LAST.png', 660, 352],
    'Aurora Forecast - Tonight' => ['img', 'https://services.swpc.noaa.gov/experimental/images/aurora_dashboard/tonights_static_viewline_forecast.png', 'https://services.swpc.noaa.gov/experimental/images/aurora_dashboard/tonights_static_viewline_forecast.png', 512, 512],
    'Aurora Forecast - 30 mins' => ['img', 'https://services.swpc.noaa.gov/images/animations/ovation/north/latest.jpg', 'https://services.swpc.noaa.gov/images/animations/ovation/north/latest.jpg', 512, 512],
    'GOES19 CCOR-1' => ['img', 'https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg', 'https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg', 512, 512],
    'LASCO C2' => ['img', 'https://soho.nascom.nasa.gov/data/realtime/c2/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c2/1024/latest.jpg', 512, 512],
    'LASCO C3' => ['img', 'https://soho.nascom.nasa.gov/data/realtime/c3/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c3/1024/latest.jpg', 512, 512],
    'Geomagnetic Latitude' => ['table', 'sun/site.geomag.pos.lat.tsv', '', 0, 0],
];

$ran = rand(1,1000);

foreach ($imgurl as $title => $url) {
    if ($url[0] == 'table') {
        echo "<h2>$title</h2>";
        $filename = $url[1];
        display_table_from_tsv($filename, 1);
        echo "<br/>";
        continue;
    } else if ($url[0] = 'img') {
        echo "<h2>$title</h2>";
        echo "<a href='$url[2]?=$ran' target='_blank'><img width='$url[3]' height='$url[4]' src='$url[1]?=$ran' alt='$title' /></a>";
    }
}

?>
</td></tr>
</table>
<?php include('tail.php'); ?>
</body>
</html>
