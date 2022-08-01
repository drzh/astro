<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';

$ele = 15;
$st = '';
if (isset($_GET['st'])) {
  $st = $_GET['st'];
}


$files = glob('list/data/*.csv');
$n = 0;

# Main submenu
foreach ($files as $f) {
  $e = explode('.', basename($f));
  $s = $e[0];
  if ($st == $s) {
    echo '<div class="citem">', $s, '</div><br/>';
  }
  else {
    echo '<a href="list.php?st=', $s, '">', $s, '</a><br/>';
  }
}

if ($st != '') {
  $fname = 'list/data/' . $st . '.csv';
  if (file_exists($fname)) {
    echo '<table bgcolor="gray" cellspacing="1" cellpadding="3">';
    $fh = fopen($fname, "r") or die("Cannot open file $fname!\n");
    $n = 0;
    while(! feof($fh)) {
      // $e = explode("\t", fgetcsv($fh));
      $e = fgetcsv($fh);
      if ($n == 0) {
      }
      else {
      }
      if ($n % 2 == 0) {
        echo '<tr bgcolor="#DDDDDD">';
      }
      else {
        echo '<tr bgcolor="#FFFFFF">';
      }
      foreach ($e as $v) {
            echo '<td align="center">';
            echo $v;
            echo '</td>';
      }
      echo '</tr>';
      $n++;
    }
    echo '</table>';
  }
}

include('tail.php');
?>
</body>
</html>
