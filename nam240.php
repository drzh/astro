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

echo 'Day: ';
$i = 0;
$iend = 41;
$istep = 4;
while ($i < $iend) {
  if ($i > 0) {
    echo ' | ';
  }
  echo  '<a href="nam240.php?bg=', $i, '&ed=', ($i + $istep - 1 < $iend) ? ($i + $istep -1) : $iend, '">', ($i + $istep - 4) / 4, '</a>';
  $i += $istep;
}
echo '<br/>';

if ($begin >= 0) {
  $i = $begin;
  while ($i <= $end) {
    $id = sprintf("%02d", $i);
    echo '<br/><img src="https://weatherstreet.com/gfs_files/gfs_clouds_us_', $i, '.png" alt="', $i, '"><br/>';
    $i++;
  }
}

include('tail.php');
?>
</body>
</html>
