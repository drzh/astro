<!DOCTYPE html>
<html>
<?php include("head.php"); ?>
<body>
<?php include('menu.php'); ?>
<?php include('libtable.php'); ?>
<table>
<tr style='vertical-align:top'><td>
<?php
echo "<h2>NOAA Aurora Forecast - 3 Days</h2>";
$filename = 'sun/aurora.3day.tsv';
display_table_from_tsv($filename, 1);
echo "<br/>";

$imgurl = [
    'GFZ Aurora Forecast - 3 Days' => ['https://spaceweather.gfz.de/fileadmin/SW-Monitor/kp_swift_ensemble_LAST.png', 'https://spaceweather.gfz.de/fileadmin/SW-Monitor/kp_swift_ensemble_LAST.png', 660, 352],
    'Aurora Forecast - Tonight' => ['https://services.swpc.noaa.gov/experimental/images/aurora_dashboard/tonights_static_viewline_forecast.png', 'https://services.swpc.noaa.gov/experimental/images/aurora_dashboard/tonights_static_viewline_forecast.png', 512, 512],
    'Aurora Forecast - 30 mins' => ['https://services.swpc.noaa.gov/images/animations/ovation/north/latest.jpg', 'https://services.swpc.noaa.gov/images/animations/ovation/north/latest.jpg', 512, 512],
    'GOES19 CCOR-1' => ['https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg', 'https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg', 512, 512],
    'LASCO C2' => ['https://soho.nascom.nasa.gov/data/realtime/c2/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c2/1024/latest.jpg', 512, 512],
    'LASCO C3' => ['https://soho.nascom.nasa.gov/data/realtime/c3/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c3/1024/latest.jpg', 512, 512],
];

$ran = rand(1,1000);

foreach ($imgurl as $title => $url) {
    echo "<h2>$title</h2>";
    echo "<a href='$url[1]?=$ran' target='_blank'><img width='$url[2]' height='$url[3]' src='$url[0]?=$ran' alt='$title' /></a>";
}

echo "<br/>";
echo "<h2>Geomagnetic Latitude</h2>";
$filename = 'sun/site.geomag.pos.lat.tsv';
display_table_from_tsv($filename, 1);
echo "<br/>";

?>
</td></tr>
</table>
<?php include('tail.php'); ?>
</body>
</html>
