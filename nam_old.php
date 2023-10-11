<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';

$begin = -1;
$end = -1;
if (isset($_GET['bg'])) {
  $begin = $_GET['bg'];
}
if (isset($_GET['ed'])) {
  $end = $_GET['ed'];
}
if ($begin > 0 && $end < 0) {
  $end = $begin;
}
if ($end > 0 && $begin < 0) {
  $begin = $end;
}

$i = 1;
$iend = 29;
$istep = 3;
while ($i < $iend) {
  if ($i > 1) {
    echo ' | ';
  }
  echo  '<a href="nam.php?bg=', $i, '&ed=', $i + $istep - 1, '">', ($i - 1) * 3, '-', ($i + $istep - 1 - 1) * 3, '</a>';
  $i += $istep;
}
echo '<br/>';

if ($begin > 0) {
  $i = $begin;
  while ($i <= $end) {
    $id = sprintf("%02d", $i);
    echo '<img src="http://ready.arl.noaa.gov/data/forecast/grads/nam/panel10/plt', $i, '.gif">';
    $i++;
  }
}

include('tail.php');
?>
</body>
</html>
