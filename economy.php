<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';

require 'plot.inc';

$param = array(
  'marginleft' => 50,
  'margintop' => 20,
  'marginright' => 50,
  'marginbottom' => 90,
  'mainwidth' => 800,
  'mainheight' => 200,
  'xbreaks' => '',
  'ybreaks' => '',
  'xmin' => '',
  'xmax' => '',
  'ymin' => '',
  'ymax' => '',
);

# Treasury rate
$fs = array(
  array(
    'name' => 'economy/treasury/treasury_rate.300d.data',
    'title' => 'Treasury Rate',
    'ytype' => array(
      '3Mo' => 'sccir6',
      '1Yr' => 'sccir1',
      '2Yr' => 'sccir2',
      '5Yr' => 'sccir3',
      '10Yr' => 'sccir4',
      '30Yr' => 'sccir5',
    ),
    'cxstep' => 100
  ),
#  array(
    #'name' => 'economy/treasury/treasury_rate.100d.diff.data',
    #'title' => 'Treasury Rate Difference',
    #'ytype' => array(
      #//'1YEAR-1YEAR' => 'sccir1',
      #//'2YEAR-1YEAR' => 'sccir3',
      #//'5YEAR-1YEAR' => 'sccir2',
      #//'10YEAR-1YEAR' => 'sccir4',
      #//'30YEAR-1YEAR' => 'sccir6',
      #'3MONTH-3MONTH' => 'sccir1',
      #'1YEAR-3MONTH' => 'sccir3',
      #'2YEAR-3MONTH' => 'sccir2',
      #'5YEAR-3MONTH' => 'sccir7',
      #'10YEAR-3MONTH' => 'sccir4',
      #'30YEAR-3MONTH' => 'sccir6',
    #),
    #'cxstep' => 170
  #),
);
foreach ($fs as $f) {
  $x = array();
  $y = array();
  $xlab = array();
  $type = array();
  $fh = fopen($f['name'], "r");
  if ($fh) {
    while(! feof($fh)) {
      $e = explode("\t", trim(fgets($fh)));
      $len = count($e);
      if ($len > 3) {
        array_push($x, $e[0]);
        array_push($xlab, $e[1]);
        array_push($y, $e[2]);
        array_push($type, $f['ytype'][$e[3]]);
      }
    }
    fclose($fh);
    # calc scale
    $diff = max($y) - min($y);
    $mag = 1e-2;
    while ($diff / $mag > 2) {
      $mag *= 20;
    }
    $mag /= 20;
    # plot
    $param['ymin'] = floor(min($y) / $mag) * $mag;
    $param['ymax'] = ceil(max($y) / $mag) * $mag;
    $param['ybreaks'] = range($param['ymin'], $param['ymax'], $mag);
    echo '<h3>', $f['title'], '</h3>';
    $cx = $param['marginleft'];
    $cxstep = $f['cxstep'];
    echo '<svg style="width:', 100 + $cx + $cxstep * count($f['ytype']), 'px; height:20px">';
    foreach (array_keys($f['ytype']) as $k) {
      echo '<circle class="', $f['ytype'][$k], '" cx=', $cx, ' cy=10 r=2></circle>';
      echo '<text class="', $f['ytype'][$k], '" x=', $cx + 10, ' y=15>', $k, '</text>';
      $cx += $cxstep;
    }
    echo '</svg>';
    plotsvg($x, $y, $xlab, $param, $type);
  } else {
    echo "<p>Cannot open file: $fname!</p>";
  }
}

## CPI data
#$fs = array(
  #array(
    #'name' => 'economy/cpi/AllItems.10y.cpi.data',
    #'title' => 'CPI - All Items - 10 years'
  #),
  #array(
    #'name' => 'economy/cpi/AllItems.36m.cpi.data',
    #'title' => 'CPI - All Items - 36 months'
  #),
#);
#foreach ($fs as $f) {
  #$x = array();
  #$xlab = array();
  #$y = array();
  #$fh = fopen($f['name'], "r");
  #if ($fh) {
    #while(! feof($fh)) {
      #$e = explode("\t", trim(fgets($fh)));
      #$len = count($e);
      #if ($len > 1) {
         #array_push($x, $e[0]);
         #array_push($xlab, $e[1]);
         #array_push($y, $e[2]);
      #}
    #}
    #fclose($fh);
    ## calc scale
    #$diff = max($y) - min($y);
    #$mag = 1;
    #while ($diff / $mag > 4) {
      #$mag *= 2;
    #}
    #$mag /= 2;
    ## plot
    #$param['ymin'] = floor(min($y) / $mag) * $mag;
    #$param['ymax'] = ceil(max($y) / $mag) * $mag;
    #$param['ybreaks'] = range($param['ymin'], $param['ymax'], $mag);
    #echo '<h3>', $f['title'], '</h3>';
    #plotsvg($x, $y, $xlab, $param);
  #} else {
    #echo "<p>Cannot open file: $fname</p>";
  #}
#}

echo "<hr>\n";

include('tail.php');
?>
</body>
</html>
