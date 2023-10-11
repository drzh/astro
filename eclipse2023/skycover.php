<!DOCTYPE html>
<html>
<?php include("../head.php") ?>

<body>
<?php
  $datadir = '/home/celaeno/web/astro/skycover';
  $f1 = $datadir . '/all.skycover.3day.UTC.format';
  $f2 = $datadir . '/all.rhm.3day.UTC.format';
  $f3 = $datadir . '/all.temp.3day.UTC.format';
  
  require '../site/site.eclipse2023.php';
  require '../menu.php';
  include '../plot_weather.php';
  include('../tail.php');
  ?>
</body>

</html>
