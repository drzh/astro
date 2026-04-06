<!DOCTYPE html>
<html>
<?php include('head.php') ?>
<body>
<script src="cloud.js"></script>
<?php
require 'site/site.php';
require 'menu.php';

function cloudus_nav_item($href, $label, $active)
{
  if ($active) {
    return '<span class="citem">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
  }

  return '<a class="menu-state-link" href="' . $href . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';
}

$size = '';
if (isset($_GET['sz']) && ($_GET['sz'] === '1' || $_GET['sz'] === '2')) {
  $size = $_GET['sz'];
}

$small_clouds = array(
  array('Visible', 'http://www.ssd.noaa.gov/goes/comp/ceus/vis.jpg'),
  array('Rainbow', 'http://www.ssd.noaa.gov/goes/comp/ceus/rb.jpg'),
  array('Shortwave (IR2)', 'http://www.ssd.noaa.gov/goes/comp/ceus/ir2.jpg'),
  array('Water Vapor (IR3)', 'http://www.ssd.noaa.gov/goes/comp/ceus/wv.jpg'),
);

$cloud = array(
  array('Visible', 'vis-l.jpg', 'h5-mloop-vis.html', 'visable'),
  array('Rainbow', 'rb-l.jpg', 'h5-mloop-rb.html', 'rainbow'),
  array('Shortwave (IR2)', 'ir2-l.jpg', 'h5-mloop-ir2.html', 'shortwave'),
  array('Water Vapor (IR3)', 'wv-l.jpg', 'h5-mloop-wv.html', 'water'),
);

$latpx = array(
  500 => 17,
  475 => 159,
  450 => 293,
  425 => 421,
  400 => 543,
  375 => 663,
  350 => 778,
  325 => 890,
  300 => 998,
  275 => 1104,
  250 => 1207,
);

$testlongs = range(-120, -80, 2.5);
$testlats = range(30, 50, 2.5);
$testlong1 = -120;
$testlong2 = -80;
$testlat1 = 50;
$testlat2 = 30;

function calclong($long)
{
  return intval(37.15 * $long + 4743);
}

function calclat($lat)
{
  global $latpx;

  $lat = $lat * 10;
  $step = 25;
  $latlow = floor($lat / $step) * $step;
  $lathigh = $latlow + $step;
  if (isset($latpx[$latlow]) && isset($latpx[$lathigh])) {
    return intval($latpx[$latlow] + ($lat - $latlow) / ($lathigh - $latlow) * ($latpx[$lathigh] - $latpx[$latlow]));
  }
  if (isset($latpx[$latlow])) {
    return intval($latpx[$latlow] + ($lat - $latlow) / $step * ($latpx[$latlow] - $latpx[$latlow - $step]));
  }
  if (isset($latpx[$lathigh])) {
    return intval($latpx[$lathigh] - ($lathigh - $lat) / $step * ($latpx[$lathigh + $step] - $latpx[$lathigh]));
  }

  return 0;
}

function plotmarker($lat, $long, $type = 'line1')
{
  $lenbar = 5;
  $x = calclong($long);
  $y = calclat($lat);
  echo '<line x1="', $x - $lenbar, '" y1="', $y, '" x2="', $x + $lenbar, '" y2="', $y, '" class="', $type, '" />';
  echo '<line x1="', $x, '" y1="', $y - $lenbar, '" x2="', $x, '" y2="', $y + $lenbar, '" class="', $type, '" />';
}

function plotmarkerlabel($title, $lat, $long, $iddiv)
{
  $lenbar = 5;
  $x = calclong($long);
  $y = calclat($lat);
  echo '<rect name="', htmlspecialchars($title, ENT_QUOTES, 'UTF-8'), '" x="', $x - $lenbar, '" y="', $y - $lenbar, '" width="', $lenbar * 2, '" height="', $lenbar * 2, '" fill-opacity="0" onmousemove="showtooltip(evt, ', $x, ', ', $y, ', \'', htmlspecialchars($iddiv, ENT_QUOTES, 'UTF-8'), '\')" onmouseout="hidetooltip(\'', htmlspecialchars($iddiv, ENT_QUOTES, 'UTF-8'), '\')" />', "\n";
}

function plotpath($path)
{
  $n = count($path);
  $i = 0;
  while ($i < $n - 1) {
    $p1 = $path[$i];
    $p2 = $path[$i + 1];
    $j = 1;
    while ($j < 6) {
      if (is_numeric($p1[$j]) && is_numeric($p1[$j + 1]) && is_numeric($p2[$j]) && is_numeric($p2[$j + 1])) {
        echo '<line stroke-dasharray="1,3" x1="', calclong($p1[$j + 1]), '" y1="', calclat($p1[$j]), '" x2="', calclong($p2[$j + 1]), '" y2="', calclat($p2[$j]), '" class="line3" />';
      }
      $j += 2;
    }
    $i++;
  }
}

function load_cloudus_path($fpath)
{
  $path = array();
  if (!file_exists($fpath)) {
    return $path;
  }

  $fh = fopen($fpath, 'r');
  if (!$fh) {
    return $path;
  }

  while (($line = fgets($fh)) !== false) {
    $e = explode("\t", rtrim($line));
    if (count($e) > 6) {
      $path[] = $e;
    }
  }
  fclose($fh);

  return $path;
}

$path = load_cloudus_path('data/path.format');

echo '<section class="panel">';
echo '<div class="filter-bar">';
echo '<span class="filter-label">View Size</span>';
echo '<div class="chip-row">';
echo cloudus_nav_item('/cloudus.php?sz=1', 'Small', $size === '1');
echo cloudus_nav_item('/cloudus.php?sz=2', 'Big', $size === '2');
echo '</div>';
echo '</div>';
echo '</section>';

if ($size === '1') {
  echo '<div class="weather-stack">';
  foreach ($small_clouds as $item) {
    echo '<section class="panel">';
    echo '<h2 class="panel-title">', htmlspecialchars($item[0], ENT_QUOTES, 'UTF-8'), '</h2>';
    echo '<figure class="media-panel">';
    echo '<img src="', htmlspecialchars($item[1], ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($item[0] . ' cloud view', ENT_QUOTES, 'UTF-8'), '">';
    echo '</figure>';
    echo '</section>';
  }
  echo '</div>';
} elseif ($size === '2') {
  echo '<div class="weather-stack">';
  foreach ($cloud as $c) {
    $ran = rand(1, 1000);
    $tooltip_id = 'cloudus-' . preg_replace('/[^a-z0-9_-]+/i', '', $c[3]);
    $composite_width = 2317;
    $composite_height = 1300;
    echo '<section class="panel">';
    echo '<h2 class="panel-title">', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), '</h2>';
    echo '<figure class="media-panel media-panel--tight image-scroll">';
    echo '<div class="responsive-stage-frame" style="width:', $composite_width, 'px; height:', $composite_height, 'px;">';
    echo '<div class="cloud-composite responsive-stage" data-stage-width="', $composite_width, '" data-stage-height="', $composite_height, '" style="width:', $composite_width, 'px; height:', $composite_height, 'px;">', "\n";
    echo '<img style="top:106px; left:1597px; width:720px; height:480px;" src="http://www.ssd.noaa.gov/goes/east/ne/', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Northeast ', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), ' cloud mosaic"><br/>', "\n";
    echo '<img style="top:14px; left:1166px; width:840px; height:559px;" src="http://www.ssd.noaa.gov/goes/east/gl/', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Great Lakes ', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), ' cloud mosaic"><br/>', "\n";
    echo '<img style="top:0; left:558px; width:720px; height:480px;" src="http://www.ssd.noaa.gov/goes/east/np/', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Northern Plains ', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), ' cloud mosaic"><br/>', "\n";
    echo '<img style="top:0; left:0; width:720px; height:480px;" src="http://www.ssd.noaa.gov/goes/east/nw/', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Northwest ', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), ' cloud mosaic"><br/>', "\n";
    echo '<img style="top:400px; left:1412px; width:720px; height:480px;" src="http://www.ssd.noaa.gov/goes/east/ma/', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Mid Atlantic ', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), ' cloud mosaic"><br/>', "\n";
    echo '<img style="top:352px; left:966px; width:720px; height:480px;" src="http://www.ssd.noaa.gov/goes/east/mw/', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Midwest ', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), ' cloud mosaic"><br/>', "\n";
    echo '<img style="top:352px; left:558px; width:720px; height:480px;" src="http://www.ssd.noaa.gov/goes/east/cp/', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Central Plains ', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), ' cloud mosaic"><br/>', "\n";
    echo '<img style="top:352px; left:0; width:720px; height:480px;" src="http://www.ssd.noaa.gov/goes/east/wc/', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="West Coast ', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), ' cloud mosaic"><br/>', "\n";
    echo '<img style="top:715px; left:149px; width:720px; height:480px;" src="http://www.ssd.noaa.gov/goes/east/sw/', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Southwest ', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), ' cloud mosaic"><br/>', "\n";
    echo '<img style="top:758px; left:706px; width:720px; height:480px;" src="http://www.ssd.noaa.gov/goes/east/sc/', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="South Central ', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), ' cloud mosaic"><br/>', "\n";
    echo '<img style="top:801px; left:1226px; width:720px; height:480px;" src="http://www.ssd.noaa.gov/goes/east/se/', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Southeast ', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), ' cloud mosaic"><br/>', "\n";
    echo '<svg style="top:0; left:0; width:', $composite_width, 'px; height:', $composite_height, 'px;" onload="init(evt)">', "\n";
    foreach ($testlats as $lat) {
      plotmarker($lat, $testlong1, 'line2');
      plotmarker($lat, $testlong2, 'line2');
    }
    foreach ($testlongs as $long) {
      plotmarker($testlat1, $long, 'line2');
      plotmarker($testlat2, $long, 'line2');
    }
    if (count($path) > 0) {
      plotpath($path);
    }
    foreach ($pos as $p) {
      plotmarker($p[1], $p[2], 'line1');
    }
    foreach ($pos as $p) {
      plotmarkerlabel($p[0], $p[1], $p[2], $tooltip_id);
    }
    echo '</svg>', "\n";
    echo '</div>';
    echo '<span class="tooltip" id="', htmlspecialchars($tooltip_id, ENT_QUOTES, 'UTF-8'), '" style="position:absolute; visibility:hidden"></span>', "\n";
    echo '</div>';
    echo '</figure>';
    echo '</section>';
  }
  echo '</div>';
} else {
  echo '<section class="panel"><p class="page-note">Choose Small or Big to load the current CloudUS imagery.</p></section>';
}

include('tail.php');
?>
</body>
</html>
