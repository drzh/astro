<!DOCTYPE html>
<html>
<?php include("../head.php") ?>
<?php include("../libplot.php") ?>

<body>
  <script src="../cloud.js">
  </script>
  <?php
  require '../menu.php';

  $pa = '';
  if (isset($_GET['pa'])) {
    $pa = $_GET['pa'];
  }

  $fproj = '../site/site.lp.tx.proj';

  # Read the markers
  $marker = array();
  if ($fproj != '') {
    // read marker
    $i = 0;
    $fh = fopen($fproj, "r") or die("Cannot open file!\n");
    while (!feof($fh)) {
      if ($row = fgets($fh)) {
        $e = explode("\t", rtrim($row));
        array_push($marker, $e);
      }
    }
    fclose($fh);
  }

  # Read path files
  $paths = [];
  if ($pa != '') {
    $dir = 'site/';
    foreach (explode(',', $pa) as $p) {
      $fn =  $dir . 'path.' . $p . '.nam84.proj';
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

  $it = 'LP';
  $rg = 'TX';
  $stylepos = 'top: 0px; left: 0px; width:' . $scale[$it][$rg]['w'] . 'px; height: ' . $scale[$it][$rg]['h'] . 'px;';
  echo '<div style="position:relative; ', $stylepos, '">';
  $ran = rand(1, 1000000);
  echo '<img src="NorthAmerica2024B-TX.png">';
  echo '<svg style="position:absolute; ', $stylepos, '" onload="init(evt)">';
  // plot path
  foreach ($paths as $path) {
    plotpath($path, $it, $rg, 'path1');
    plotpath($path, $it, $rg, 'path2');
  }
  // plot marker
  foreach ($marker as $m) {
    plotmarker($m[1], $m[2], $it, $rg, 'line1');
    plotmarkerlabel($m[1], $m[2], $it, $rg, $m[0], $rg . '_' . $i);
  }
  echo '</svg>';
  echo '<span class="tooltip" id="', $rg . '_' . $i, '" style="position:absolute; visibility:hidden"> </span>';
  echo '</div>';
  #echo '<br/>';
  $i++;

  include('../tail.php');
  ?>
</body>

</html>
