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
$imgurlbase = 'http://sdo.gsfc.nasa.gov/assets/img/latest/latest_';
$imgurl = [
    'HMI Continuum' => 'HMIIC.jpg',
    'AIA 304 &Aring / 50,000 K / Transition region / Chromosphere' => '0304.jpg',
    'AIA 193 &Aring / 1 millon K / Corona / Flare plasma' => '0193.jpg',
    'AIA 131 &Aring / 10 millon K / Flaring region' => '0131.jpg',
];

$ran = rand(1,1000000);

foreach ($imgurl as $title => $url) {
    echo "<h2>$title</h2>";
    echo "<a href='${imgurlbase}${rsl_link}_$url?$=$ran' target='_blank'><img src='${imgurlbase}${rsl}_$url?=$ran' alt='$title' /></a>";
}

?>
</td></tr>