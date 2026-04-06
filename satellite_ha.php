<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';
require_once __DIR__ . '/includes/table.php';

$max = 20;
$sat = '';
$mag = 3.0;
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
$satellite_lists = array();

foreach ($files as $f) {
  $e = explode('.', basename($f));
  $satellite_lists[] = $e[0];
}
sort($satellite_lists, SORT_NATURAL);

$mag_options = astro_numeric_options(array(1.0, 1.5, 2.0, 2.5, 3.0, 4.0, 5.0, 6.0), (float) $mag);
$max_options = astro_numeric_options(array(10, 20, 30, 40, 50, 75, 100), $max);

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

echo '<section class="panel">';
echo '<form class="filter-form filter-form--compact" method="get" action="satellite_ha.php">';
echo '<div class="filter-field">';
echo '<label class="filter-field__label" for="satellite-ha-name">Visible Pass List</label>';
echo '<select class="filter-select" id="satellite-ha-name" name="sat" onchange="this.form.submit()">';
echo astro_select_option('', 'Choose list', $sat === '');
foreach ($satellite_lists as $name) {
  echo astro_select_option($name, $name, $sat === $name);
}
echo '</select>';
echo '</div>';
echo '<div class="filter-field">';
echo '<label class="filter-field__label" for="satellite-ha-mag">Max Magnitude</label>';
echo '<select class="filter-select" id="satellite-ha-mag" name="mag" onchange="this.form.submit()">';
foreach ($mag_options as $option) {
  echo astro_select_option(number_format((float) $option, 1, '.', ''), number_format((float) $option, 1, '.', ''), (float) $mag === (float) $option);
}
echo '</select>';
echo '</div>';
echo '<div class="filter-field">';
echo '<label class="filter-field__label" for="satellite-ha-max">Rows</label>';
echo '<select class="filter-select" id="satellite-ha-max" name="max" onchange="this.form.submit()">';
foreach ($max_options as $option) {
  echo astro_select_option($option, $option, $max === (int) $option);
}
echo '</select>';
echo '</div>';
echo '<noscript><button class="filter-submit" type="submit">Apply</button></noscript>';
echo '</form>';
echo '</section>';

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
  echo '<p class="page-note">Choose a visible-pass list from the dropdown to view results.</p>';
}
echo '</section>';

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
