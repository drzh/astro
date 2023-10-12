<!DOCTYPE html>
<html>
<?php include("head.php") ?>

<body>
  <?php
  $datadir = '/home/celaeno/web/astro/nam';
  $f1 = $datadir . '/all.skycover.84hr.UTC.format';
  $f2 = '';
  $f3 = '';
  
  require 'site/site.php';
  require 'menu.php';
  include 'plot_weather.php';
  include('tail.php');
  ?>
</body>

</html>