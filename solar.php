<!DOCTYPE html>
<html>
<?php include("head.php"); ?>
<body>
<?php include('menu.php'); ?>
<table>
<tr style='vertical-align:top'><td>
<?php
$rsl = '256';
$imgurl = [
    'HMI Continuum' => 'http://sdo.gsfc.nasa.gov/assets/img/latest/latest_' . $rsl . '_HMIIC.jpg',
    'AIA 304 &Aring / 50,000 K / Transition region / Chromosphere' => 'http://sdo.gsfc.nasa.gov/assets/img/latest/latest_' . $rsl . '_0304.jpg',
    'AIA 193 &Aring / 1 millon K / Corona / Flare plasma' => 'http://sdo.gsfc.nasa.gov/assets/img/latest/latest_' . $rsl . '_0193.jpg',
    'AIA 131 &Aring / 10 millon K / Flaring region' => 'http://sdo.gsfc.nasa.gov/assets/img/latest/latest_' . $rsl . '_0131.jpg',
];

$ran = rand(1,1000000);

foreach ($imgurl as $title => $url) {
    echo "<h2>$title</h2>";
    echo "<img src=$url?=$ran alt='$title' />";
}

?>
</td></tr>