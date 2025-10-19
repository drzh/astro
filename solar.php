<!DOCTYPE html>
<html>
<?php include("head.php"); ?>
<body>
<?php include('menu.php'); ?>
<table>
<tr style='vertical-align:top'><td>
<?php
$rsl = '256';
$rsl_link = '1024';
$imgurlbase1 = 'http://sdo.gsfc.nasa.gov/assets/img/latest/latest_';
$imgurl = [
    'HMI Continuum' => [$imgurlbase1 . $rsl . '_HMIIC.jpg', $imgurlbase1 . $rsl_link . '_HMIIC.jpg', 256, 256],
    'AIA 304 &Aring / 50,000 K / Transition region / Chromosphere' => [$imgurlbase1 . $rsl . '_0304.jpg', $imgurlbase1 . $rsl_link . '_0304.jpg', 256, 256],
    #'AIA 193 &Aring / 1 millon K / Corona / Flare plasma' => [$imgurlbase1 . $rsl . '_0193.jpg', $imgurlbase1 . $rsl_link . '_0193.jpg'],
    #'AIA 131 &Aring / 10 millon K / Flaring region' => [$imgurlbase1 . $rsl . '_0131.jpg', $imgurlbase1 . $rsl_link . '_0131.jpg'],
    'LASCO C2' => ['https://soho.nascom.nasa.gov/data/realtime/c2/1024/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c2/1024/latest.jpg', 256, 256],
    'LASCO C3' => ['https://soho.nascom.nasa.gov/data/realtime/c3/1024/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c3/1024/latest.jpg', 256, 256],
    'Solar Cycle Sunspot Number Progression' => ['https://helioforecast.space//static/sync/icme_solar_cycle/cycle25_prediction_focus.png', 'https://helioforecast.space//static/sync/icme_solar_cycle/cycle25_prediction_focus.png', 600, 300],
];

$ran = rand(1,1000000);

foreach ($imgurl as $title => $url) {
    echo "<h2>$title</h2>";
    echo "<a href='$url[1]?$=$ran' target='_blank'><img width='$url[2]' height='$url[3]' src='$url[0]?=$ran' alt='$title' /></a>";
}

?>
</td></tr>
