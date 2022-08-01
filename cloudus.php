<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<script src="cloud.js">
</script>
<?php
require 'site/site.php';
require 'menu.php';

$size = 0;
if (isset($_GET['sz'])) {
  $size = $_GET['sz'];
}
?>
<a href='/cloudus.php?sz=1'>Small</a> | 
<a href='/cloudus.php?sz=2'>Big</a>
<br/>
<?php
if ($size == '1') {
  echo 'Visible<br/><img src="http://www.ssd.noaa.gov/goes/comp/ceus/vis.jpg"><br/>';
  echo 'Rainbow<br/><img src="http://www.ssd.noaa.gov/goes/comp/ceus/rb.jpg"><br/>';
  echo 'Shortwave (IR2)<br/><img src="http://www.ssd.noaa.gov/goes/comp/ceus/ir2.jpg"><br/>';
  echo 'Water Vapor (IR3)<br/><img src="http://www.ssd.noaa.gov/goes/comp/ceus/wv.jpg"><br/>';
}

if ($size == '2') {
  $cloud = array(
    array(
      'Visible',
      'vis-l.jpg',
      'h5-mloop-vis.html',
      'visable'
    ),
    array(
      'Rainbow',
      'rb-l.jpg',
      'h5-mloop-rb.html',
      'rainbow'
    ),
    array(
      'Shortwave (IR2)',
      'ir2-l.jpg',
      'h5-mloop-ir2.html',
      'shortwave'
    ),
    array(
      'Water Vapor (IR3)',
      'wv-l.jpg',
      'h5-mloop-wv.html',
      'water'
    )
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
    250 => 1207
  );
  
  $testlongs = range(-120, -80, 2.5);
  $testlats = range(30, 50, 2.5);
  $testlong1 = -120;
  $testlong2 = -80;
  $testlat1 = 50;
  $testlat2 = 30;
  
  function calclong($long) {
    return intval(37.15 * $long + 4743);
  }
    
  function calclat($lat) {
    global $latpx;
    $lat = $lat * 10;
    $step = 25;
    $latlow = floor($lat / $step) * $step;
    $lathigh = $latlow + $step;
    if (isset($latpx[$latlow]) && isset($latpx[$lathigh])) {
      return intval($latpx[$latlow] + ($lat - $latlow) / ($lathigh - $latlow) * ($latpx[$lathigh] - $latpx[$latlow]));
    }
    else if (isset($latpx[$latlow])) {
      return intval($latpx[$latlow] + ($lat - $latlow) / $step * ($latpx[$latlow] - $latpx[$latlow - $step]));
    }
    else if (isset($latpx[$lathigh])) {
      return intval($latpx[$lathigh] - ($lathigh - $lat) / $step * ($latpx[$lathigh + $step] - $latpx[$lathigh]));
    }
    else {
      return 0;
    }
  }

  function plotmarker($lat, $long, $type = 'line1') {
    $lenbar = 5;
    echo "<line x1='", calclong($long) - $lenbar, "' y1='", calclat($lat), "' x2='", calclong($long) + $lenbar, "' y2='", calclat($lat), "' class='", $type, "' />";
    echo "<line x1='", calclong($long), "' y1='", calclat($lat) - $lenbar, "' x2='", calclong($long), "' y2='", calclat($lat) + $lenbar, "' class='", $type, "' />";
  }
    
  function plotmarkerlabel($title, $lat, $long, $iddiv) {
    $lenbar = 5;
    echo "<rect name='", $title, "' x='", calclong($long) - $lenbar, "' y='", calclat($lat) - $lenbar, "' x2='", calclong($long) + $lenbar, "' width='", $lenbar * 2, "' height='", $lenbar * 2, "' fill-opacity='0' onmousemove='showtooltip(evt, ", calclong($long), ", ", calclat($lat), ", \"", $iddiv, "\")' onmouseout='hidetooltip(\"", $iddiv, "\")' />", "\n";
  }
    
  function plotpath($path) {
    $lenbar = 5;
    $type = 'line1';
    $n = count($path);
    $i = 0;
    while ($i < $n - 1) {
      $p1 = $path[$i];
      $p2 = $path[$i + 1];
      $j = 1;
      while ($j < 6) {
        if (is_numeric($p1[$j]) && is_numeric($p1[$j + 1]) && is_numeric($p2[$j]) && is_numeric($p2[$j + 1])) {
          echo "<line stroke-dasharray='1,3' x1='", calclong($p1[$j + 1]), "' y1='", calclat($p1[$j]), "' x2='", calclong($p2[$j + 1]), "' y2='", calclat($p2[$j]), "' class='line3' />";
        }
        $j += 2;
      }
      $i++;
    }
  }
    
  /* Read and process path.format */
  $path = array();
  $fpath = 'data/path.format';
  if (glob($fpath)) {
    $fh = fopen($fpath, "r") or die("Cannot open file: " . $fpath . "!\n");
    while(! feof($fh)) {
      $e = explode("\t", rtrim(fgets($fh)));
      if (count($e) > 6) {
        array_push($path, $e);
        /* array_push($path, array($e[1], $e[2], $e[3], $e[4], $e[5], $e[6])); */
      }
    }
  }
  /* End of path.format */

  foreach ($cloud as $c) {
    echo "$c[0]";
    echo "<div class='cloudimg2'>", "\n";
    $ran = rand(1,1000);
    echo "<img style='position:absolute; top: 106px; left: 1597px; width: 720px; height: 480px;' src='http://www.ssd.noaa.gov/goes/east/ne/$c[1]?=$ran'><br/>", "\n";
    echo "<img style='position:absolute; top: 14px; left: 1166px; width: 840px; height: 559px;' src='http://www.ssd.noaa.gov/goes/east/gl/$c[1]?=$ran'><br/>", "\n";
    echo "<img style='position:absolute; top: 0px; left: 558px; width: 720px; height: 480px;' src='http://www.ssd.noaa.gov/goes/east/np/$c[1]?=$ran'><br/>", "\n";
    echo "<img style='position:absolute; top: 0px; left: 0px; width: 720px; height: 480px;' src='http://www.ssd.noaa.gov/goes/east/nw/$c[1]?=$ran'><br/>", "\n";
    
    echo "<img style='position:absolute; top: 400px; left: 1412px; width: 720px; height: 480px;' src='http://www.ssd.noaa.gov/goes/east/ma/$c[1]?=$ran'><br/>", "\n";
    echo "<img style='position:absolute; top: 352px; left: 966px; width: 720px; height: 480px;' src='http://www.ssd.noaa.gov/goes/east/mw/$c[1]?=$ran'><br/>", "\n";
    echo "<img style='position:absolute; top: 352px; left: 558px; width: 720px; height: 480px;' src='http://www.ssd.noaa.gov/goes/east/cp/$c[1]?=$ran'><br/>", "\n";
    echo "<img style='position:absolute; top: 352px; left: 0px; width: 720px; height: 480px;' src='http://www.ssd.noaa.gov/goes/east/wc/$c[1]?=$ran'><br/>", "\n";
    
    echo "<img style='position:absolute; top: 715px; left: 149px; width: 720px; height: 480px;' src='http://www.ssd.noaa.gov/goes/east/sw/$c[1]?=$ran'><br/>", "\n";
    echo "<img style='position:absolute; top: 758px; left: 706px; width: 720px; height: 480px;' src='http://www.ssd.noaa.gov/goes/east/sc/$c[1]?=$ran'><br/>", "\n";
    echo "<img style='position:absolute; top: 801px; left: 1226px; width: 720px; height: 480px;' src='http://www.ssd.noaa.gov/goes/east/se/$c[1]?=$ran'><br/>", "\n";
    
    echo "<svg style='position:absolute; top: 0px; left: 0px; width: 2000px; height: 1300px;'>", "\n";
    // Plot alignment markers
    foreach ($testlats as $lat) {
      plotmarker($lat, $testlong1, 'line2');
      plotmarker($lat, $testlong2, 'line2');
    }
    foreach ($testlongs as $long) {
      plotmarker($testlat1, $long, 'line2');
      plotmarker($testlat2, $long, 'line2');
    }
    // Plot path
    if (count($path) > 0) {
      plotpath($path);
    }
    // Plot markers
    foreach ($pos as $p) {
      plotmarker($p[1], $p[2], 'line1');
    }
    // Plot marker label
    foreach ($pos as $p) {
      plotmarkerlabel($p[0], $p[1], $p[2], $c[3]);
    }
    
    echo "</svg>", "\n";
    echo "<span class='tooltip' id='", $c[3], "' style='position:absolute; visibility:hidden'> </span>", "\n";
    echo "</div>", "\n";
    echo "<hr/>", "\n";
  }
}

include('tail.php');
?>
</body>
</html>
