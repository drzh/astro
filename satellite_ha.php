<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';
include('libtable.php');

$maxurl = 20;
$max = 20;
$sat = '';
$magurl = 3.0;
$mag = $magurl;
if (isset($_GET['sat'])) {
  $sat = $_GET['sat'];
}
if (isset($_GET['max'])) {
  $max = (int) $_GET['max'];
}
if (isset($_GET['mag'])) {
  $mag = (float) $_GET['mag'];
}

$maxalert = 20;
$files = glob('satellite/ha/*.tsv');
$current = time();
$year = date('y');
$alertstime = array();
$alertsmsg = array();
$offset = 60 * 3;
$headers = array();
$rows = array();
$sort_values = array();

ob_start();
echo '<div class="menu-stack menu-stack--column">';
foreach ($files as $f) {
  $e = explode('.', basename($f));
  $s = $e[0];
  if ($sat == $s) {
    echo '<div class="citem">', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'), '</div>';
  } else {
    echo '<a class="menu-state-link" href="satellite_ha.php?sat=', urlencode($s), '&mag=', $magurl, '&max=', $maxurl, '">', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'), '</a>';
  }
}
echo '</div>';
$sidebar_menu = ob_get_clean();

if ($sat != '') {
  $fname = 'satellite/ha/' . $sat . '.tsv';
  if (file_exists($fname)) {
    $fh = fopen($fname, 'r') or die("Cannot open file $fname!\n");
    $count = 0;
    while (!feof($fh)) {
      $line = fgets($fh);
      if ($line === false) {
        continue;
      }
      $e = explode("\t", rtrim($line));
      if (count($e) <= 1) {
        continue;
      }

      if (empty($headers)) {
        $headers = $e;
        $headers[1] = 'Date';
        continue;
      }

      $ed = explode('-', $e[1]);
      if (count($ed) !== 2) {
        continue;
      }
      $monday = $ed[0];
      $starttime = strtotime($monday . '/' . $year . ' ' . $ed[1]);
      if ($starttime < $current || (float) $e[2] >= $mag) {
        continue;
      }
      if ($count >= $max) {
        break;
      }

      if ($starttime - $offset > $current) {
        $alertsmsg[] = $e[0] . ' , Mag: ' . $e[2];
        $alertstime[] = $starttime - $offset;
      }

      $display = $e;
      $display[1] = date('D, n/j', $starttime);
      $rows[] = $display;
      $sort_values[] = array(
        $display[0],
        $starttime,
        (float) $display[2],
        astro_time_to_seconds($display[3]),
        (int) preg_replace('/[^0-9\-]/', '', $display[4]),
        $display[5],
        astro_time_to_seconds($display[6]),
        (int) preg_replace('/[^0-9\-]/', '', $display[7]),
        $display[8],
        astro_time_to_seconds($display[9]),
        (int) preg_replace('/[^0-9\-]/', '', $display[10]),
        $display[11],
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
  echo '<span class="page-toolbar__label">List: ', htmlspecialchars($sat, ENT_QUOTES, 'UTF-8'), '</span>';
}
echo '<span class="page-toolbar__label">Max magnitude: ', htmlspecialchars((string) $mag, ENT_QUOTES, 'UTF-8'), '</span><span class="page-toolbar__label">Rows: ', htmlspecialchars((string) $max, ENT_QUOTES, 'UTF-8'), '</span>';
echo '</div>';
if (!empty($headers)) {
  render_sortable_table($headers, $rows, $sort_values, array('empty_message' => 'No visible passes match the current filters.'));
} else {
  echo '<p class="page-note">Choose a visible-pass list from the sidebar to view results.</p>';
}
echo '</section>';
echo '</div>';

$tgalert = 'config/tgsatvisalt.off';
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
