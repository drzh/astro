<?php

$state = '';
if (isset($_GET['st'])) {
  $state = $_GET['st'];
}

$pagewt = 'index.php';
echo 'State: ';
echo '<a href="', $pagewt, '?st=all">All</a>';

foreach (array_unique(array_column($pos, 7)) as $s) {
  if ($s != 'any') {
    echo ' | <a href="', $pagewt, '?st=', $s, '">', $s, '</a>';
  }
}

foreach ($pos as $p) {
  if ($state != 'all' && $p[7] != 'any' && $p[7] != $state) {
    continue;
  }
  $ran = rand(1,1000000);
  echo "<h2><a href='$p[4]' target='_blank'>$p[0]</a>";
  if ($p[2] <= 360 && $p[1] <= 90) {
    echo " (<a href='http://maps.google.com/maps?q=$p[1],$p[2]' target='_blank'>$p[1], $p[2]</a>)";
  }
  echo "</h2>";
  $ran = rand(1,1000000);
  if ($p[3] != '') {
    echo "<img src='$p[3]?=$ran'>&nbsp;", "\n";
  }
  if ($p[2] <= 360 && $p[1] <= 90) {
    echo "<img src='https://www.7timer.info/bin/astro.php?lon=$p[2]&lat=$p[1]&lang=en&ac=0&unit=metric&output=internal&tzshift=0&v=$ran'>&nbsp;&nbsp;", "\n";
  }
  if ($p[6] != '') {
    echo "<div class='noaa1'>";
    echo "<img style='position:absolute; top:0px; left:0px;' src='$p[6]?=$ran'>";
    $ahour = preg_replace('/ahour=0/', 'ahour=48', $p[6]);
    echo "<img style='position:absolute; top:0px; left:800px;' src='$ahour?=$ran'>";
    echo "</div>", "\n";
  }
  echo "<hr>", "\n";
}
?>
