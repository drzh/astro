<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';

$para_st = '';
if (isset($_GET['st'])) {
  $para_st = $_GET['st'];
}
$para_day = '';
if (isset($_GET['day'])) {
  $para_day = $_GET['day'];
}

$fs = glob('skycover/img/*.png');
$state = array();
foreach ($fs as $f) {
  $e = explode('.', basename($f));
  if (! array_key_exists($e[0], $state)) {
    $state[$e[0]] = array();
  }
  array_push($state[$e[0]], $e[1]);
}

foreach (array_keys($state) as $s) {
  echo $s, ' : ';
  $daypre = '';
  foreach (array_values($state[$s]) as $t) {
    $t = strtotime($t . '00 UTC');
    $daytag = date('D, n/j', $t);
    $day = date('n/j', $t);
    if ($daypre != $day) {
      if ($daypre != '') {
        echo ' | ';
      }
      echo '<a href="skycoverus.php?st=', $s, '&day=', $day, '">', $daytag, '</a>';
      $daypre = $day;
    }
  }
  echo '<br/>';
}

foreach ($fs as $f) {
  $e = explode('.', basename($f));
  $s = $e[0];
  $t = strtotime($e[1] . '00 UTC');
  $day = date('n/j', $t);
  if ($s == $para_st && $day == $para_day) {
    echo date('D, n/j, H:i', $t), '<br/>';
    echo '<img src="', $f, '"><img src="skycover/legend.png"><br/>';
  }
}

include('tail.php');
?>
</body>
</html>
