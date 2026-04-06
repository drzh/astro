<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';
require_once __DIR__ . '/includes/table.php';

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
$priority_satellites = array();
$other_satellites = array();

$fh = fopen($filepri, 'r') or die("Cannot open file $filepri!\n");
while (!feof($fh)) {
  $s = trim((string) fgets($fh));
  if ($s === '') {
    continue;
  }
  $satpri[$s] = 1;
  $fname = 'satellite/data/sat.' . $s . '.table.tsv';
  if (file_exists($fname)) {
    $priority_satellites[] = $s;
  }
}
fclose($fh);

foreach ($files as $f) {
  $e = explode('.', basename($f));
  $s = $e[1];
  if (!array_key_exists($s, $satpri)) {
    $other_satellites[] = $s;
  }
}
sort($other_satellites, SORT_NATURAL);

$elevation_options = astro_numeric_options(array(5, 10, 15, 20, 25, 30, 40, 50, 60, 75, 90), $ele);
$max_options = astro_numeric_options(array(10, 20, 30, 40, 50, 75, 100), $max);

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

echo '<section class="panel">';
echo '<form class="filter-form filter-form--compact" method="get" action="satellite.php">';
echo '<div class="filter-field">';
echo '<label class="filter-field__label" for="satellite-name">Satellite</label>';
echo '<select class="filter-select" id="satellite-name" name="sat" onchange="this.form.submit()">';
echo astro_select_option('', 'Choose satellite', $sat === '');
if (!empty($priority_satellites)) {
  echo '<optgroup label="Priority">';
  foreach ($priority_satellites as $name) {
    echo astro_select_option($name, $name, $sat === $name);
  }
  echo '</optgroup>';
}
if (!empty($other_satellites)) {
  echo '<optgroup label="All Satellites">';
  foreach ($other_satellites as $name) {
    echo astro_select_option($name, $name, $sat === $name);
  }
  echo '</optgroup>';
}
echo '</select>';
echo '</div>';
echo '<div class="filter-field">';
echo '<label class="filter-field__label" for="satellite-ele">Min Elevation</label>';
echo '<select class="filter-select" id="satellite-ele" name="ele" onchange="this.form.submit()">';
foreach ($elevation_options as $option) {
  echo astro_select_option($option, $option . ' deg', $ele === (int) $option);
}
echo '</select>';
echo '</div>';
echo '<div class="filter-field">';
echo '<label class="filter-field__label" for="satellite-max">Rows</label>';
echo '<select class="filter-select" id="satellite-max" name="max" onchange="this.form.submit()">';
foreach ($max_options as $option) {
  echo astro_select_option($option, $option, $max === (int) $option);
}
echo '</select>';
echo '</div>';
echo '<div class="filter-actions"><a class="menu-state-link" href="satellite/freq.php">Frequencies</a></div>';
echo '<noscript><button class="filter-submit" type="submit">Apply</button></noscript>';
echo '</form>';
echo '</section>';

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
  echo '<p class="page-note">Choose a satellite from the dropdown to view upcoming passes.</p>';
}
echo '</section>';

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
