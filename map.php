<!DOCTYPE html>
<html>
<?php include("head.php"); ?>
<body>
<?php include('menu.php'); ?>
<table>
<tr style='vertical-align:top'><td>
<?php
$imgurl = [
    'World Magnetic Model - Epoch 2025.0 Main Field Inclination (I)' => ['map/World_Magnetic_Model_Epoch_2025.0_Main_Field_Inclination.png', 'map/World_Magnetic_Model_Epoch_2025.0_Main_Field_Inclination.png', 518, 432]
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