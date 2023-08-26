<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';

$maxurl = 20;
$max = 20;
$sat = '';
$magurl = 3.0;
if (isset($_GET['sat'])) {
  $sat = $_GET['sat'];
}
if (isset($_GET['max'])) {
  $max = $_GET['max'];
}
if (isset($_GET['mag'])) {
  $mag = $_GET['mag'];
}

$maxalert = 20;

$files = glob('satellite/ha/*.tsv');

echo '<table><tr><td valign="top">';
# Main submenu
foreach ($files as $f) {
  $e = explode('.', basename($f));
  $s = $e[0];
  if ($sat == $s) {
    echo '<div class="citem">', $s, '</div><br/>';
  }
  else {
    echo '<a href="satellite_ha.php?sat=', $s, '&mag=', $magurl, '&max=', $maxurl, '">', $s, '</a><br/>';
  }
}
echo '</td><td valign="top">';
$current = time();
$year = date('y');
$alertstime = array();
$alertsmsg = array();
$offset = 60 * 3; // alert offset in seconds;
if ($sat != '') {
  $fname = 'satellite/ha/' . $sat . '.tsv';
  if (file_exists($fname)) {
    echo '<table bgcolor="gray" cellspacing="1" cellpadding="3">';
    $fh = fopen($fname, "r") or die("Cannot open file $fname!\n");
    $n = 0;
    while(! feof($fh)) {
      if ($n > $max) break;
      $e = explode("\t", rtrim(fgets($fh)));
      if (count($e) > 1) {
        if ($n == 0) {
          $starttime = $current;
          $e[1] = 'Date';
        }
        else {
          $ed = explode('-', $e[1]);
          $monday = $ed[0];
          $time = $ed[1];
          $starttime = strtotime($monday . '/' . $year . ' ' . $time);
          $e[1] = date('D, n/j', $starttime);
        }
        if ($n == 0 || $starttime >= $current && $e[2] < $mag) {
          if ($starttime - $offset > $current) {
            array_push($alertsmsg, $e[0] . ' , Mag: ' . $e[2]);
            array_push($alertstime, $starttime - $offset);
          }
          if ($n % 2 == 0) {
            echo '<tr bgcolor="#444444">';
          }
          else {
            echo '<tr bgcolor="#333333">';
          }
          foreach ($e as $ele) {
            echo '<td align="center">';
            echo $ele;
            echo '</td>';
          }
          echo '</tr>';
          $n++;
        }
      }
    }
    echo '</table>';
  }
}
echo '</td></tr></table>', "\n";

$tgalert = 'config/tgsatvisalt.off';
if (! file_exists($tgalert)) {
  $nalert = min(sizeof($alertstime), $maxalert);
  $now = time();
  echo '<script>';
  include('counteralert.js');
  echo "\n";
  for ($i = 0; $i < $nalert; $i++) {
    if ($now < $alertstime[$i]) {
      // echo $alerttime, ' ', $now, ' ', $alertmsg;
      echo 'counteralert(', ($alertstime[$i] - $now) * 1000, ', "', $alertsmsg[$i], '");', "\n";
    }
  }
  echo '</script>';
}

include('tail.php');
?>
</body>
</html>
