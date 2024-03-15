<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<?php include("libplot.php") ?>

<body>
  <script src="cloud.js">
  </script>
  <?php

  require 'menu.php';

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

  ## Set $rg and $it to the default value if it is not set
  if ($rg == '') {
    $rg = 'TX';
  }
  if ($it == '') {
    $it = 'JPG';
  }

  $imgpre = array(
    'FullDisk' => 'https://cdn.star.nesdis.noaa.gov/GOES16/ABI/FD/',
    'CONUS' => 'https://cdn.star.nesdis.noaa.gov/GOES16/ABI/CONUS/',
    'TX' => 'https://cdn.star.nesdis.noaa.gov/GOES16/ABI/SECTOR/sp/',
  );

  $imgpost = array(
    'JPG' => array(
      'FullDisk' => '/678x678.jpg',
      'CONUS' => '/1250x750.jpg',
      'TX' => '/1200x1200.jpg'
    ),
    'GIF' => array(
      'FullDisk' => '/678x678.gif',
      'CONUS' => '/1250x750.gif',
      'TX' => '/1200x1200.gif'
    )
  );

  $channeltype = array(
    'GEOCOLOR' => 'GeoColor',
    // 'AirMass' => 'AirMass',
    // 'Sandwich' => 'Sandwich',
    // 'DayCloudPhase' => 'DayCloudPhase',
    // 'NightMicrophysics' => 'NightMicrophysics',
    '01' => '0.47µm, Visible - Blue',
    '02' => '0.64µm, Visible - Red',
    '03' => '0.86µm, Near IR - Vegetation',
    '04' => '1.37µm, Near IR - Cirrus',
    '05' => '1.6µm, Near IR - Snow/Ice',
    '06' => '2.2µm, Near IR - Cloud Ice',
    '07' => '3.9µm, IR - Shortwave',
    '08' => '6.2µm, IR - Upper-Level Water Vapor',
    '09' => '6.9µm, IR - Mid-Level Water Vapor',
    '10' => '7.3µm, IR - Lower-Level Water Vapor',
    '11' => '8.4µm, IR - Cloud Top Phase',
    '12' => '9.6µm, IR - Ozone',
    '13' => '10.3µm, IR - Clean Longwave',
    '14' => '11.2µm, IR - Standard Longwave',
    '15' => '12.3µm, IR - Dirty Longwave',
    '16' => '13.32µm, IR - CO2 Longwave'
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

  // submenu
  // Region menu
  $region = array('FullDisk', 'CONUS', 'TX');
  echo '<table>';
  $flag = 0;
  echo '<tr>';
  echo '<td>Region:&nbsp;</td>';
  echo '<td>';
  foreach ($region as $r) {
    if ($flag == 0) {
      $flag = 1;
    } else {
      echo ' | ';
    }
    if ($rg == $r) {
      echo '<div class="citem">', $r, '</div>';
    } else {
      echo '<a href="cloud.php?', "rg=$r", ($ch == '') ? '' : "&ch=$ch", ($it == '') ? '' : "&it=$it", ($pa == '') ? '' : "&pa=$pa", '">', $r, '</a>';
    }
  }
  echo '</td>';
  echo '</tr>';

  // Channel menu
  $flag = 0;
  echo '<tr>';
  echo '<td>Channel:&nbsp;</td>';
  echo '<td>';
  foreach (array('All' => 'All') + $channeltype as $c => $n) {
    if ($flag == 0) {
      $flag = 1;
    } else {
      echo ' | ';
    }
    if ($ch == $c) {
      echo '<div class="citem">', $c, '</div>';
    } else {
      echo '<a href="cloud.php?', ($rg == '') ? '' : "rg=$rg", "&ch=$c", ($it == '') ? '' : "&it=$it", ($pa == '') ? '' : "&pa=$pa", '">', $c, '</a>';
    }
  }
  echo '</td>';
  echo '</tr>';

  // Image type menu
  $flag = 0;
  echo '<tr>';
  echo '<td>Image:&nbsp;</td>';
  echo '<td>';
  foreach ($imgtype as $i => $n) {
    if ($flag == 0) {
      $flag = 1;
    } else {
      echo ' | ';
    }
    if ($it == $i) {
      echo '<div class="citem">', $i, '</div>';
    } else {
      echo '<a href="cloud.php?', ($rg == '') ? '' : "rg=$rg", ($ch == '') ? '' : "&ch=$ch", "&it=$i", ($pa == '') ? '' : "&pa=$pa", '">', $i, '</a>';
    }
  }
  echo '</td>';
  echo '</tr>';

  echo '</table>';

  $fproj = 'goes/site.fulldisk.proj';

  if ($rg != '' && $ch != '') {
    // read marker
    $marker = array();
    $i = 0;
    $fh = fopen($fproj, "r") or die("Cannot open file!\n");
    while (!feof($fh)) {
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
          while (!feof($fh)) {
            if ($row = fgets($fh)) {
              $e = explode("\t", rtrim($row));
              if (!array_key_exists($e[0], $path)) {
                $path[$e[0]] = [];
              }
              array_push($path[$e[0]], $e);
            }
          }
          foreach ($path as $p) {
            array_push($paths, $p);
          }
        }
      }
    }

    // plot img and marker
    $chs = array();
    if ($ch == 'All') {
      $chs = array_keys($channeltype);
    } else {
      $chs = array($ch);
    }
    $flag = array();
    $timediff = 60;
    foreach ($chs as $c) {
      echo $rg, ' : ', $c, ' : ', $channeltype[$c], '<br/>';
      $stylepos = 'top: 0px; left: 0px; width:' . $scale[$it][$rg]['w'] . 'px; height: ' . $scale[$it][$rg]['h'] . 'px;';
      echo '<div style="position:relative; ', $stylepos, '">';
      $ran = rand(1, 1000000);
      echo '<img src="', getimg($it, $rg, $c), '?=', $ran, '">';
      echo '<svg style="position:absolute; ', $stylepos, '" onload="init(evt)">';
      // plot path
      foreach ($paths as $path) {
        plotpath($path, $it, $rg, 'path1');
        plotpath($path, $it, $rg, 'path2');
      }
      // plot marker
      foreach ($marker as $m) {
        plotmarker($m[1], $m[2], $it, $rg, 'line1');
        plotmarkerlabel($m[1], $m[2], $it, $rg, $m[0], $rg . $c);
      }
      echo '</svg>';
      echo '<span class="tooltip" id="', $rg, $c, '" style="position:absolute; visibility:hidden"> </span>';
      echo '</div>';
      if (isset($colorbar[$c]) && $colorbar[$c] != '') {
        echo '<br/><img src="', $colorbar[$c], '"><br/>';
      }
    }
  }

  echo "<hr>\n";

  include('tail.php');
  ?>
</body>

</html>