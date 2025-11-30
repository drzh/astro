<!DOCTYPE html>
<html>
<?php include("head.php"); ?>
<body>
<?php include('menu.php'); ?>
<table>
<tr style='vertical-align:top'><td>
<?php
$imgurl = [
    'World Magnetic Model - Epoch 2020.0 Geomagnetic Coordinates' => ['map/Geomagnetic_Coordinates.jpg', 'map/Geomagnetic_Coordinates.jpg', 777, 648],
    'World Magnetic Model - Epoch 2025.0 Main Field Declination (D)' => ['https://www.ncei.noaa.gov/sites/g/files/anmtlf171/files/inline-images/D.jpg', 'https://www.ncei.noaa.gov/sites/g/files/anmtlf171/files/inline-images/D.jpg', 691, 576],
    'World Magnetic Model - Epoch 2025.0 Main Field Inclination (I)' => ['https://www.ncei.noaa.gov/sites/g/files/anmtlf171/files/inline-images/I.jpg', 'https://www.ncei.noaa.gov/sites/g/files/anmtlf171/files/inline-images/I.jpg', 691, 576],
];

$ran = rand(1,1000);

foreach ($imgurl as $title => $url) {
    echo "<h2>$title</h2>";
    echo "<a href='$url[1]' target='_blank'><img width='$url[2]' height='$url[3]' src='$url[0]' alt='$title' /></a>";
}

?>
</td></tr>
</table>
<?php include('tail.php'); ?>
</body>
</html>
