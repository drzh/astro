<!DOCTYPE html>
<html>
<?php include('head.php') ?>
<?php include('libplot.php') ?>
<body>
<script src="cloud.js"></script>
<?php
require 'menu.php';

function cloud_nav_item($href, $label, $active)
{
  if ($active) {
    return '<span class="citem">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
  }

  return '<a class="menu-state-link" href="' . $href . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';
}

$rg = '';
$ch = '';
$it = '';
$pa = '';
if (isset($_GET['rg'])) {
  $rg = $_GET['rg'];
}
if (isset($_GET['ch'])) {
  $ch = $_GET['ch'];
}
if (isset($_GET['pa'])) {
  $pa = $_GET['pa'];
}
if (isset($_GET['it'])) {
  $it = $_GET['it'];
}
if ($rg == '') {
  $rg = 'TX';
}
if ($ch == '') {
  $ch = 'GEOCOLOR';
}
if ($it == '') {
  $it = 'JPG';
}

$channeltype = array(
  'GEOCOLOR' => 'GeoColor',
  '01' => '0.47um, Visible - Blue',
  '02' => '0.64um, Visible - Red',
  '03' => '0.86um, Near IR - Vegetation',
  '04' => '1.37um, Near IR - Cirrus',
  '05' => '1.6um, Near IR - Snow/Ice',
  '06' => '2.2um, Near IR - Cloud Ice',
  '07' => '3.9um, IR - Shortwave',
  '08' => '6.2um, IR - Upper-Level Water Vapor',
  '09' => '6.9um, IR - Mid-Level Water Vapor',
  '10' => '7.3um, IR - Lower-Level Water Vapor',
  '11' => '8.4um, IR - Cloud Top Phase',
  '12' => '9.6um, IR - Ozone',
  '13' => '10.3um, IR - Clean Longwave',
  '14' => '11.2um, IR - Standard Longwave',
  '15' => '12.3um, IR - Dirty Longwave',
  '16' => '13.32um, IR - CO2 Longwave'
);

$imgtype = array(
  'JPG' => 'jpg',
  'GIF' => 'gif'
);

$colorbar = array(
  'GEOCOLOR' => '',
  'AirMass' => '',
  'Sandwish' => '',
  'DayCloudPhase' => '',
  'NightMicrophysics' => '',
  '01' => '',
  '02' => '',
  '03' => '',
  '04' => '',
  '05' => '',
  '06' => '',
  '07' => 'https://www.star.nesdis.noaa.gov/GOES/images/colorbars/ColorBar450Band7_horz.png',
  '08' => 'https://www.star.nesdis.noaa.gov/GOES/images/colorbars/ColorBar450Bands8-10_horz.png',
  '09' => 'https://www.star.nesdis.noaa.gov/GOES/images/colorbars/ColorBar450Bands8-10_horz.png',
  '10' => 'https://www.star.nesdis.noaa.gov/GOES/images/colorbars/ColorBar450Bands8-10_horz.png',
  '11' => 'https://www.star.nesdis.noaa.gov/GOES/images/colorbars/ColorBar450Bands11-15_horz.png',
  '12' => 'https://www.star.nesdis.noaa.gov/GOES/images/colorbars/ColorBar450Bands11-15_horz.png',
  '13' => 'https://www.star.nesdis.noaa.gov/GOES/images/colorbars/ColorBar450Bands11-15_horz.png',
  '14' => 'https://www.star.nesdis.noaa.gov/GOES/images/colorbars/ColorBar450Bands11-15_horz.png',
  '15' => 'https://www.star.nesdis.noaa.gov/GOES/images/colorbars/ColorBar450Bands11-15_horz.png',
  '16' => 'https://www.star.nesdis.noaa.gov/GOES/images/colorbars/ColorBar450Band16_horz.png'
);

$region = array('FullDisk', 'CONUS', 'TX');

echo '<section class="panel">';
echo '<div class="page-toolbar">';
echo '<span class="page-toolbar__label">Region</span>';
echo '<div class="chip-row">';
foreach ($region as $r) {
  $href = 'cloud.php?rg=' . urlencode($r) . ($ch == '' ? '' : '&ch=' . urlencode($ch)) . ($it == '' ? '' : '&it=' . urlencode($it)) . ($pa == '' ? '' : '&pa=' . urlencode($pa));
  echo cloud_nav_item($href, $r, $rg == $r);
}
echo '</div>';
echo '</div>';

echo '<div class="page-toolbar">';
echo '<span class="page-toolbar__label">Channel</span>';
echo '<div class="chip-row">';
foreach (array('All' => 'All') + $channeltype as $c => $n) {
  $href = 'cloud.php?' . ($rg == '' ? '' : 'rg=' . urlencode($rg)) . '&ch=' . urlencode($c) . ($it == '' ? '' : '&it=' . urlencode($it)) . ($pa == '' ? '' : '&pa=' . urlencode($pa));
  echo cloud_nav_item($href, $c, $ch == $c);
}
echo '</div>';
echo '</div>';

echo '<div class="page-toolbar">';
echo '<span class="page-toolbar__label">Image</span>';
echo '<div class="chip-row">';
foreach ($imgtype as $i => $n) {
  $href = 'cloud.php?' . ($rg == '' ? '' : 'rg=' . urlencode($rg)) . ($ch == '' ? '' : '&ch=' . urlencode($ch)) . '&it=' . urlencode($i) . ($pa == '' ? '' : '&pa=' . urlencode($pa));
  echo cloud_nav_item($href, $i, $it == $i);
}
echo '</div>';
echo '</div>';
echo '</section>';

$fproj = 'goes/site.fulldisk.proj';
if ($rg != '' && $ch != '') {
  $marker = array();
  $fh = fopen($fproj, 'r') or die("Cannot open file!\n");
  while (!feof($fh)) {
    if ($row = fgets($fh)) {
      $e = explode("\t", rtrim($row));
      $marker[] = $e;
    }
  }
  fclose($fh);

  $paths = [];
  if ($pa != '') {
    $dir = 'site/';
    foreach (explode(',', $pa) as $p) {
      $fn = $dir . 'path.' . $p . '.proj';
      if (file_exists($fn)) {
        $fh = fopen($fn, 'r') or die("Cannot open $fn\n");
        $path = [];
        while (!feof($fh)) {
          if ($row = fgets($fh)) {
            $e = explode("\t", rtrim($row));
            if (!array_key_exists($e[0], $path)) {
              $path[$e[0]] = [];
            }
            $path[$e[0]][] = $e;
          }
        }
        fclose($fh);
        foreach ($path as $path_rows) {
          $paths[] = $path_rows;
        }
      }
    }
  }

  $chs = $ch == 'All' ? array_keys($channeltype) : array($ch);
  $scroll_group = count($chs) > 1 ? 'cloud-' . md5($rg . '|' . $it . '|' . $pa . '|' . implode(',', $chs)) : '';
  foreach ($chs as $c) {
    $stylepos = 'top:0; left:0; width:' . $scale[$it][$rg]['w'] . 'px; height:' . $scale[$it][$rg]['h'] . 'px;';
    $ran = rand(1, 1000000);
    echo '<section class="panel">';
    echo '<h2 class="panel-title">', htmlspecialchars($rg . ' : ' . $c . ' : ' . $channeltype[$c], ENT_QUOTES, 'UTF-8'), '</h2>';
    echo '<figure class="media-panel image-scroll"';
    if ($scroll_group !== '') {
      echo ' data-scroll-sync-group="', htmlspecialchars($scroll_group, ENT_QUOTES, 'UTF-8'), '"';
    }
    echo '>';
    echo '<div class="responsive-stage-frame" style="width:', (int) $scale[$it][$rg]['w'], 'px; height:', (int) $scale[$it][$rg]['h'], 'px;">';
    echo '<div class="responsive-stage" data-stage-width="', (int) $scale[$it][$rg]['w'], '" data-stage-height="', (int) $scale[$it][$rg]['h'], '" style="position:relative; ', $stylepos, '">';
    echo '<img src="', htmlspecialchars(getimg($it, $rg, $c), ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="', htmlspecialchars($rg . ' ' . $c . ' cloud image', ENT_QUOTES, 'UTF-8'), '">';
    echo '<svg style="position:absolute; ', $stylepos, '" onload="init(evt)">';
    foreach ($paths as $path) {
      plotpath($path, $it, $rg, 'path1');
      plotpath($path, $it, $rg, 'path2');
    }
    foreach ($marker as $m) {
      plotmarker($m[1], $m[2], $it, $rg, 'line1');
      plotmarkerlabel($m[1], $m[2], $it, $rg, $m[0], $rg . $c);
    }
    echo '</svg>';
    echo '<span class="tooltip" id="', htmlspecialchars($rg . $c, ENT_QUOTES, 'UTF-8'), '" style="position:absolute; visibility:hidden"> </span>';
    echo '</div>';
    echo '</div>';
    echo '</figure>';
    if (isset($colorbar[$c]) && $colorbar[$c] != '') {
      echo '<figure class="media-panel">';
      echo '<span class="media-panel__label">Color Bar</span>';
      echo '<img src="', htmlspecialchars($colorbar[$c], ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($c . ' color bar', ENT_QUOTES, 'UTF-8'), '">';
      echo '</figure>';
    }
    echo '</section>';
  }
}

include('tail.php');
?>
</body>
</html>
