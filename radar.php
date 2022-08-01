<!DOCTYPE html>
<html>
<?php include("head.php") ?>

<body>
  <?php
  require 'menu.php';
  ?>
  <div style="position:relative; top: 0px; left: 0px; width:600px; height: 550px;">
    <img src="https://radar.weather.gov/ridge/lite/KFWS_0.gif?=<?php $ran = rand(1, 1000000);
                                                                echo $ran; ?>">
    <?php
    echo '<img style="position:absolute; top: 25px; left: 0px; width: 600px;" src="radar/dfws.png">'
    ?>
  </div>
  <hr>
  <img src="https://radar.weather.gov/ridge/lite/CONUS_0.gif?=<?php $ran = rand(1, 1000000);
                                                              echo $ran; ?>">
  <?php
  include('tail.php');
  ?>
</body>

</html>