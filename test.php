<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<script src="cloud.js">
</script>
<?php
require 'site/site.php';
require 'menu.php';

$pa = '';
$fproj = 'site/site.lcc.proj';

$scale = array(
  'FullDisk' => array(
    'x' => 0,
    'y' => 0,
    'w' => 678,
    'h' => 678,
    'r' => 8,
    'rx' => 8,
    'ry' => 8,
    #'ry' => 8 * 660 / 678,
  ),
  'CONUS' => array(
    'x' => 901,
    'y' => 419,
    'w' => 1250,
    'h' => 750,
    'r' => 2,
    'rx' => 2,
    'ry' => 2,
    #'ry' => 2 * 725 / 750,
  ),
  'TX' => array(
    'x' => 1409,
    'y' => 836,
    'w' => 1200,
    'h' => 1200,
    'r' => 1,
    'rx' => 0.5,
    'ry' => 0.5,
    #'ry' => 1 * 583 / 600,
  ),
  'LCC' => array(
    'x' => 0,
    'y' => 0,
    'w' => 1073,
    'h' => 689,
    'r' => 1,
    'rx' => 1,
    'ry' => 1,
    #'ry' => 1 * 583 / 600,
  ),
);

$rg = 'LCC';
$stylepos = 'top: 0px; left: 0px; width:' . $scale[$rg]['w'] . 'px; height: ' . $scale[$rg]['h'] . 'px;';

if (1) {
  // read marker
  $marker = array();
  $i = 0;
  $fh = fopen($fproj, "r") or die("Cannot open file!\n");
  while(! feof($fh)) {
    if ($row = fgets($fh)) {
      $e = explode("\t", rtrim($row));
      array_push($marker, $e);
    }
  }
  fclose($fh);

  # Read path files
  $paths = [];
  if ($pa != '') {
    $dir = 'site/';
    foreach (explode(',', $pa) as $p) {
      $fn =  $dir . 'path.' . $p . '.proj';
      if (file_exists($fn)) {
        $fh = fopen($fn, 'r') or die("Cannot open $fn\n");
        $path = [];
        while(! feof($fh)) {
          if ($row = fgets($fh)) {
            $e = explode("\t", rtrim($row));
            array_push($path, $e);
          }
        }
        if ($path) {
          array_push($paths, $path);
        }
      }
    }
  }
  
  echo '<div style="position:relative; ', $stylepos, '">';
  $ran = rand(1,1000000);
  echo '<img src="https://graphical.weather.gov/images/conus/Sky1_conus.png">';
  echo '<svg style="position:absolute; ', $stylepos, '" onload="init(evt)">';
  foreach ($marker as $m) {
    plotmarker($m[1], $m[2], $rg, 'line1');
    plotmarkerlabel($m[1], $m[2], $rg, $m[0], $rg.$c);
  }
}

function plotmarker($x, $y, $rg = 'FullDisk', $type = 'line1') {
  global $scale;
  $x = round(($x - $scale[$rg]['x']) / $scale[$rg]['rx']);
  $y = round(($y - $scale[$rg]['y']) / $scale[$rg]['ry']);
  if ($x > 0 && $x <= $scale[$rg]['w'] && $y > 0 && $y <= $scale[$rg]['h']) {
    $lenbar = ceil(3 / $scale[$rg]['r']);
    echo "<line x1='",$x - $lenbar, "' y1='", $y, "' x2='", $x + $lenbar, "' y2='", $y, "' class='", $type, "' />";
    echo "<line x1='", $x, "' y1='", $y - $lenbar, "' x2='", $x, "' y2='", $y + $lenbar, "' class='", $type, "' />";
  }
}

function plotmarkerlabel($x, $y, $rg = 'FullDisk', $title, $iddiv) {
  global $scale;
  $x = ($x - $scale[$rg]['x']) / $scale[$rg]['rx'];
  $y = ($y - $scale[$rg]['y']) / $scale[$rg]['ry'];
  if ($x > 0 && $x <= $scale[$rg]['w'] && $y > 0 && $y <= $scale[$rg]['h']) {
    $lenbar = ceil(3 / $scale[$rg]['r']);
    echo '<rect name="', $title, '" x="', $x - $lenbar, '" y="', $y - $lenbar, '" width="', $lenbar * 2, '" height="', $lenbar * 2, '" fill-opacity="0" onmousemove="showtooltip(evt, ', $x, ', ', $y, ', \'', $iddiv, '\')" onmouseout="hidetooltip(\'', $iddiv, '\')" />';
  }
}

function plotpath($path, $rg = 'FullDisk', $type = 'line1') {
  global $scale;
  $d = '';
  $i = 0;
  foreach ($path as $p) {
    $x = $p[1];
    $y = $p[2];
    $x = round(($x - $scale[$rg]['x']) / $scale[$rg]['rx']);
    $y = round(($y - $scale[$rg]['y']) / $scale[$rg]['ry']);
    if ($x > 0 && $x <= $scale[$rg]['w'] && $y > 0 && $y <= $scale[$rg]['h']) {
      if ($i == 0) {
        $d = 'M' . $x . ' ' . $y;
        $i = 1;
      }
      else {
        $d = $d . ' L' . $x . ' ' . $y;
      }
      /* echo $x, ', ', $y, '<br>'; */
    }
  }
  echo '<path d="', $d, '" fill-opacity="0" class="', $type, '" />';
}

echo "<hr>\n";

include('tail.php');
?>
</body>
</html>
