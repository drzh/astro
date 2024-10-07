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
    'HMI Continuum' => [$imgurlbase1 . $rsl . '_HMIIC.jpg', $imgurlbase1 . $rsl_link . '_HMIIC.jpg'],
    'AIA 304 &Aring / 50,000 K / Transition region / Chromosphere' => [$imgurlbase1 . $rsl . '_0304.jpg', $imgurlbase1 . $rsl_link . '_0304.jpg'],
    #'AIA 193 &Aring / 1 millon K / Corona / Flare plasma' => [$imgurlbase1 . $rsl . '_0193.jpg', $imgurlbase1 . $rsl_link . '_0193.jpg'],
    #'AIA 131 &Aring / 10 millon K / Flaring region' => [$imgurlbase1 . $rsl . '_0131.jpg', $imgurlbase1 . $rsl_link . '_0131.jpg'],
    'LASCO C2' => ['https://soho.nascom.nasa.gov/data/realtime/c2/1024/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c2/1024/latest.jpg'],
    'LASCO C3' => ['https://soho.nascom.nasa.gov/data/realtime/c3/1024/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c3/1024/latest.jpg'],
];

$ran = rand(1,1000000);

foreach ($imgurl as $title => $url) {
    echo "<h2>$title</h2>";
    echo "<a href='$url[1]?$=$ran' target='_blank'><img width='256' height='256' src='$url[0]?=$ran' alt='$title' /></a>";
}

?>
</td></tr>