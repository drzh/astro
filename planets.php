<!DOCTYPE html>
<html>
<?php include('head.php') ?>
<body>
<?php
require 'menu.php';
require 'planets_menu.php';
include_once 'libtable.php';

$filepattern = array(array('planet/all/*.multi_info.*.format', 'Planets'));
$today = date('Ymd');

function planets_clean_cell($value)
{
  return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
}

echo '<div class="weather-stack">';
foreach ($filepattern as $fp) {
  $headers = array();
  $sections = array();

  foreach (glob($fp[0]) as $f) {
    preg_match('/([A-Z]{3}).format/', $f, $matches, PREG_OFFSET_CAPTURE);
    $tz = $matches[1][0];
    $fh = fopen($f, 'r') or die("Cannot open file!\n");
    while (($line = fgets($fh)) !== false) {
      $e = array_map('rtrim', explode("\t", $line));
      if (count($e) != 10) {
        continue;
      }

      if (empty($headers)) {
        $headers = array_map('planets_clean_cell', array_slice($e, 1));
        continue;
      }

      $timestamp = strtotime($e[0] . ' ' . $tz);
      if ($timestamp === false) {
        continue;
      }

      $day = date('Ymd', $timestamp);
      $daydiff = (strtotime($day) - strtotime($today)) / 86400;
      if ($daydiff > 6) {
        break;
      }
      if ($daydiff < 0) {
        continue;
      }

      if (!array_key_exists($day, $sections)) {
        $sections[$day] = array(
          'label' => date('D, n/j', $timestamp),
          'rows' => array(),
        );
      }

      $row = array();
      foreach (range(1, 9) as $j) {
        $row[] = planets_clean_cell($e[$j]);
      }
      $sections[$day]['rows'][] = $row;
    }
    fclose($fh);
  }

  ksort($sections);

  if (empty($sections)) {
    echo '<section class="panel"><p class="page-note">No planetary data found in the next 7 days.</p></section>';
    continue;
  }

  foreach ($sections as $section) {
    echo '<section class="panel daily-table-section">';
    echo '<div class="chip-row"><span class="page-toolbar__label">', htmlspecialchars($section['label'], ENT_QUOTES, 'UTF-8'), '</span></div>';
    render_plain_table($headers, $section['rows']);
    echo '</section>';
  }
}
echo '</div>';

include('tail.php');
?>
</body>
</html>
