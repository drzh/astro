<!DOCTYPE html>
<html>
<?php include("head.php"); ?>
<body>
<?php include('menu.php'); ?>
<table>
<tr style='vertical-align:top'><td>
<?php
$imgurl = [
    'AIA 304' => ['sun/AIAsynoptic0304.full.png', 'sun/AIAsynoptic0304.full.png', 256, 256],
    'HMI Continuum' => ['https://soho.nascom.nasa.gov/data/realtime/hmi_igr/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/hmi_igr/1024/latest.jpg', 256, 256],
    'AIA 304 &Aring / 50,000 K / Transition region / Chromosphere' => ['https://soho.nascom.nasa.gov/data/realtime/eit_304/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/eit_304/1024/latest.jpg', 256, 256],
    'LASCO C2' => ['https://soho.nascom.nasa.gov/data/realtime/c2/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c2/1024/latest.jpg', 256, 256],
    'LASCO C3' => ['https://soho.nascom.nasa.gov/data/realtime/c3/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c3/1024/latest.jpg', 256, 256],
    'Solar Cycle Sunspot Number Progression' => ['https://helioforecast.space//static/sync/icme_solar_cycle/cycle25_prediction_focus.png', 'https://helioforecast.space//static/sync/icme_solar_cycle/cycle25_prediction_focus.png', 450, 225],
];

$ran = rand(1,1000);

foreach ($imgurl as $title => $url) {
    echo "<h2>$title</h2>";
    echo "<a href='$url[1]?=$ran' target='_blank'><img width='$url[2]' height='$url[3]' src='$url[0]?=$ran' alt='$title' /></a>";
}

?>
</td></tr>
</table>
<?php include('tail.php'); ?>
</body>
</html>