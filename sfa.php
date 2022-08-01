<!DOCTYPE html>
<html>
<?php include("head.php") ?>

<body>
  <script src="cloud.js">
  </script>
  <?php
  require 'menu.php';

  $sfa = array(
    array(
      'Mixed Surface Analysis',
      'http://images.intellicast.com/WxImages/CustomGraphic/sfcmap.gif'
    ),
    array(
      'Surface Analysis (CONUS)',
      'http://www.wpc.ncep.noaa.gov/sfc/usfntsfcwbg.gif'
    ),
  );

  foreach ($sfa as $c) {
    $ran = rand(1, 1000);
    echo $c[0], "<br />", "\n";
    echo "<img src='$c[1]?=$ran' />", "\n";
    echo "<hr>\n";
  }

  include('tail.php');
  ?>
</body>

</html>