<!DOCTYPE html>
<html>
<?php include "head.php"; ?>
<body>
<?php
require 'menu.php';
$ran = rand(1, 1000000);
?>
<section class="panel">
  <h2 class="panel-title">Dallas-Fort Worth</h2>
  <figure class="media-panel image-scroll">
    <div class="responsive-stage-frame" style="width:600px; height:550px;">
      <div class="responsive-stage" data-stage-width="600" data-stage-height="550" style="position:relative; width:600px; height:550px;">
        <img src="https://radar.weather.gov/ridge/standard/KFWS_0.gif?=<?php echo $ran; ?>" alt="Dallas-Fort Worth radar">
        <img style="position:absolute; top:25px; left:0; width:600px;" src="radar/dfws.png" alt="Dallas-Fort Worth radar overlay">
      </div>
    </div>
  </figure>
</section>
<section class="panel">
  <h2 class="panel-title">CONUS Radar</h2>
  <figure class="media-panel">
    <img class="media-panel__image--intrinsic" src="https://radar.weather.gov/ridge/standard/CONUS_0.gif?=<?php echo $ran; ?>" alt="CONUS radar" loading="lazy" decoding="async">
  </figure>
</section>
<?php include 'tail.php'; ?>
</body>
</html>
