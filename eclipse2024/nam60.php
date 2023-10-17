<!DOCTYPE html>
<html>
<?php include("../head.php") ?>

<body>
  <?php
  $datadir = '/home/celaeno/web/astro/nam';
  $f1 = $datadir . '/all.skycover.60hr.UTC.format';
  $f2 = '';
  $f3 = '';
  
  require '../site/site.eclipse2024.php';
  require '../menu.php';
  include '../plot_weather.php';
  include('../tail.php');
  ?>
</body>

</html>
