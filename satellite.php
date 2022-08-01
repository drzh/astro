<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';

$maxurl = 20;
$eleurl = 15;
$max = 20;
$ele = 15;
$sat = '';
if (isset($_GET['sat'])) {
  $sat = $_GET['sat'];
}
if (isset($_GET['max'])) {
  $max = $_GET['max'];
}
if (isset($_GET['ele'])) {
  $ele = $_GET['ele'];
}

$maxalert = 20;

$satpri = array();

echo '<table><tr><td valign="top">';
echo '<a href="satellite/freq.php">Frequencies</a><br/>----------<br/>';
$files = glob('satellite/data/sat.*.table.tsv');
$filepri = ('satellite/priority.list');
$n = 0;

# Priority submenu
$fh = fopen($filepri, "r") or die("Cannot open file $filepri!\n");
while(! feof($fh)) {
  $s = fgets($fh);
  $s = rtrim($s);
  $satpri[$s] = 1;
  $fname = 'satellite/data/sat.' . $s . '.table.tsv';
  if (file_exists($fname)) {
    if ($sat == $s) {
      echo '<div class="citem">', $s, '</div><br/>';
    }
    else {
      echo '<a href="satellite.php?sat=', $s, '&ele=', $eleurl, '&max=', $maxurl, '">', $s, '</a><br/>';
    }
  }
}
echo '----------<br/>';

# Main submenu
foreach ($files as $f) {
  $e = explode('.', basename($f));
  $s = $e[1];
  if (! array_key_exists($s, $satpri)) {
    if ($sat == $s) {
      echo '<div class="citem">', $s, '</div><br/>';
    }
    else {
      echo '<a href="satellite.php?sat=', $s, '&ele=', $eleurl, '&max=', $maxurl, '">', $s, '</a><br/>';
    }
  }
}

echo '</td><td valign="top">';
$current = time();
$alertstime = array();
$alertsmsg = array();
$offset = 60; // alert offset in seconds;
if ($sat != '') {
  $fname = 'satellite/data/sat.' . $sat . '.table.tsv';
  if (file_exists($fname)) {
    echo '<table bgcolor="gray" cellspacing="1" cellpadding="3">';
    $fh = fopen($fname, "r") or die("Cannot open file $fname!\n");
    $n = 0;
    while(! feof($fh)) {
      if ($n > $max) break;
      $e = explode("\t", fgets($fh));
      if (count($e) == 9 && ($n == 0 || $e[5] >= $ele)) {
        if ($n == 0) {
          $endtime = $current;
          $starttime = $current;
          $e[1] = str_replace('UTC', 'CDT', $e[1]);
          $e[2] = str_replace('UTC', 'CDT', $e[2]);
          $e[8] = str_replace('UTC', 'CDT', $e[8]);
        }
        else {
          $starttime = strtotime($e[1] . ' ' . $e[2] . " UTC");
          $endtime = strtotime($e[1] . ' ' . $e[8] . " UTC");
        }
        if ($endtime >= $current) {
          if ($starttime - $offset > $current) {
            array_push($alertsmsg, $e[0] . ' , Max Elevation: ' . $e[5]);
            array_push($alertstime, $starttime - $offset);
          }
          if ($n % 2 == 0) {
            echo '<tr bgcolor="#DDDDDD">';
          }
          else {
            echo '<tr bgcolor="#FFFFFF">';
          }
          $e[8] = rtrim($e[8]);
          if ($n > 0) {
            $e[1] = date("D, n/j", $starttime);
            // $e[2] = date("H:i:s", strtotime($datetime)) . ' (' . $e[2] . ')';
            $e[2] = date("H:i:s", strtotime($e[2] . ' UTC'));
            $e[8] = date("H:i:s", strtotime($e[8] . ' UTC'));
          }
          foreach (range(0, 8, 1) as $i) {
            echo '<td align="center">';
            echo $e[$i];
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

$tgalert = 'config/tgsathamalt.off';
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
