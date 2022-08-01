<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';

$sfa = array(
  array(
    'Dallas - Gamma Exposure Rate in the unit of milliRoentgen per hour (mR/h)',
    'https://www3.epa.gov/radnet00/images/exp/dallas-exp.jpg'
  ),
  array(
    'Fort Worth - Gamma Exposure Rate in the unit of milliRoentgen per hour (mR/h)',
    'https://www3.epa.gov/radnet00/images/exp/ft.worth-exp.jpg',
  ),
);

foreach ($sfa as $c) {
    $ran = rand(1,1000);
    echo $c[0], "<br />", "\n";
    echo "<img src='$c[1]?=$ran' />", "\n";
    echo "<hr>\n";
}

include('tail.php');
?>
</body>
</html>
