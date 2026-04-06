<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';
include('libtable.php');

$maxurl = 20;
$eleurl = 15;
$max = 20;
$ele = 15;
$sat = '';
if (isset($_GET['sat'])) {
  $sat = $_GET['sat'];
}
if (isset($_GET['max'])) {
  $max = (int) $_GET['max'];
}
if (isset($_GET['ele'])) {
  $ele = (int) $_GET['ele'];
}

$maxalert = 20;
$satpri = array();
$files = glob('satellite/data/sat.*.table.tsv');
$filepri = 'satellite/priority.list';
$current = time();
$alertstime = array();
$alertsmsg = array();
$offset = 60;
$headers = array();
$rows = array();
$sort_values = array();
$tz_label = date('T');

ob_start();
echo '<div class="menu-stack menu-stack--column">';
echo '<a class="menu-state-link" href="satellite/freq.php">Frequencies</a>';
echo '<span class="page-toolbar__label">Priority</span>';

$fh = fopen($filepri, 'r') or die("Cannot open file $filepri!\n");
while (!feof($fh)) {
  $s = trim((string) fgets($fh));
  if ($s === '') {
    continue;
  }
  $satpri[$s] = 1;
  $fname = 'satellite/data/sat.' . $s . '.table.tsv';
  if (file_exists($fname)) {
    if ($sat == $s) {
      echo '<div class="citem">', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'), '</div>';
    } else {
      echo '<a class="menu-state-link" href="satellite.php?sat=', urlencode($s), '&ele=', $eleurl, '&max=', $maxurl, '">', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'), '</a>';
    }
  }
}
fclose($fh);

echo '<span class="page-toolbar__label">All Satellites</span>';
foreach ($files as $f) {
  $e = explode('.', basename($f));
  $s = $e[1];
  if (!array_key_exists($s, $satpri)) {
    if ($sat == $s) {
      echo '<div class="citem">', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'), '</div>';
    } else {
      echo '<a class="menu-state-link" href="satellite.php?sat=', urlencode($s), '&ele=', $eleurl, '&max=', $maxurl, '">', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'), '</a>';
    }
  }
}
echo '</div>';
$sidebar_menu = ob_get_clean();

if ($sat != '') {
  $fname = 'satellite/data/sat.' . $sat . '.table.tsv';
  if (file_exists($fname)) {
    $fh = fopen($fname, 'r') or die("Cannot open file $fname!\n");
    $count = 0;
    while (!feof($fh)) {
      $line = fgets($fh);
      if ($line === false) {
        continue;
      }
      $e = array_map('rtrim', explode("\t", $line));
      if (count($e) !== 9) {
        continue;
      }

      if (empty($headers)) {
        $headers = $e;
        $headers[1] = str_replace('UTC', $tz_label, $headers[1]);
        $headers[2] = str_replace('UTC', $tz_label, $headers[2]);
        $headers[8] = str_replace('UTC', $tz_label, $headers[8]);
        continue;
      }

      if ((int) $e[5] < $ele) {
        continue;
      }

      $starttime = strtotime($e[1] . ' ' . $e[2] . ' UTC');
      $endtime = strtotime($e[1] . ' ' . $e[8] . ' UTC');
      if ($endtime < $current) {
        continue;
      }
      if ($count >= $max) {
        break;
      }

      if ($starttime - $offset > $current) {
        $alertsmsg[] = $e[0] . ' , Max Elevation: ' . $e[5];
        $alertstime[] = $starttime - $offset;
      }

      $display = $e;
      $display[1] = date('D, n/j', $starttime);
      $display[2] = date('H:i:s', $starttime);
      $display[8] = date('H:i:s', $endtime);
      $rows[] = $display;
      $sort_values[] = array(
        $display[0],
        $starttime,
        $starttime,
        astro_duration_to_seconds($display[3]),
        (int) $display[4],
        (int) $display[5],
        (int) $display[6],
        (int) $display[7],
        $endtime,
      );
      $count++;
    }
    fclose($fh);
  }
}

echo '<div class="split-layout">';
echo '<aside class="panel page-sidebar">';
echo $sidebar_menu;
echo '</aside>';

echo '<section class="panel">';
echo '<div class="chip-row">';
if ($sat !== '') {
  echo '<span class="page-toolbar__label">Satellite: ', htmlspecialchars($sat, ENT_QUOTES, 'UTF-8'), '</span>';
}
echo '<span class="page-toolbar__label">Min elevation: ', htmlspecialchars((string) $ele, ENT_QUOTES, 'UTF-8'), '&deg;</span><span class="page-toolbar__label">Rows: ', htmlspecialchars((string) $max, ENT_QUOTES, 'UTF-8'), '</span>';
echo '</div>';
if (!empty($headers)) {
  render_sortable_table($headers, $rows, $sort_values, array('empty_message' => 'No upcoming passes match the current filters.'));
} else {
  echo '<p class="page-note">Choose a satellite from the list to view upcoming passes.</p>';
}
echo '</section>';
echo '</div>';

$tgalert = 'config/tgsathamalt.off';
if (!file_exists($tgalert)) {
  $nalert = min(sizeof($alertstime), $maxalert);
  $now = time();
  echo '<script>';
  include('counteralert.js');
  echo "\n";
  for ($i = 0; $i < $nalert; $i++) {
    if ($now < $alertstime[$i]) {
      echo 'counteralert(', ($alertstime[$i] - $now) * 1000, ', "', $alertsmsg[$i], '");', "\n";
    }
  }
  echo '</script>';
}

include('tail.php');
?>
</body>
</html>
