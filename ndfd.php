<!DOCTYPE html>
<html>
<?php include("head.php") ?>

<body>
  <?php
  require 'menu.php';

  $forc = 'Wx';
  $state = '';
  $begin = -1;
  $end = -1;
  if (isset($_GET['fc'])) {
    $forc = $_GET['fc'];
  }
  if (isset($_GET['st'])) {
    $state = $_GET['st'];
  }
  if (isset($_GET['bg'])) {
    $begin = $_GET['bg'];
  }
  if (isset($_GET['ed'])) {
    $end = $_GET['ed'];
  }
  if ($begin > 0 && $end < 0) {
    $end = $begin;
  }
  if ($end > 0 && $begin < 0) {
    $begin = $end;
  }

  $link1 = 'https://graphical.weather.gov/images/';
  $link2 = 'https://airquality.weather.gov/images/';
  $forecast = array(
    'Wx' => array('Weather', $link1, 3),
    'WWA' => array('Hazard', $link1, 3),
    'T' => array('Temp', $link1, 3),
    'Td' => array('Dew', $link1, 3),
    'WindSpd' => array('Wind', $link1, 3),
    'RH' => array('Humidity', $link1, 3),
    'Sky' => array('Sky', $link1, 3),
    'smokec' => array('Smoke', $link2, 1),
    'dustc' => array('Dust', $link2, 1),
  );

  foreach (array_keys($forecast) as $fc) {
    if ($forc == $fc) {
      echo '<div class="citem">', $forecast[$fc][0], '</div>';
    } else {
      echo '<a href="/ndfd.php';
      if ($fc != '') {
        echo '?fc=', $fc;
        if ($begin >= 0) {
          echo '&st=', $state, '&bg=', $begin, '&ed=', $end;
        }
      }
      echo  '">', $forecast[$fc][0], '</a>';
    }
    echo ' | ';
  }
  echo '<a href="/nam.php';
  if ($begin >= 0) {
    echo '?&st=', $state, '&bg=', floor($begin / 3) + 1, '&ed=', floor($end / 3) + 1;
  }
  echo '">NAM</a><br/>';

  $stateurl = array(
    'US' => 'conus',
    'TX' => 'southplains'
  );

  foreach (array_keys($stateurl) as $st) {
    echo $st, ': ';
    $i = 0;
    $iend = 87;
    $istep = 9;
    while ($i < $iend) {
      if ($i > 1) {
        echo ' | ';
      }
      $atext = $i . '-' . ($i + 8);
      if ($state == $st && $begin == $i && $end == $i + 8) {
        echo '<div class="citem">', $atext, '</div>';
      } else {
        echo '<a href="ndfd.php?fc=', $forc, '&st=', $st, '&bg=', $i, '&ed=', $i + 8, '">', $atext, '</a>';
      }
      $i += $istep;
    }
    echo '<br/>';
  }

  if ($begin >= 0 && $state != '') {
    $i = $begin;
    while ($i <= $end && array_key_exists($forc, $forecast)) {
      echo '<img src="', $forecast[$forc][1], $stateurl[$state], '/', $forc, $i / $forecast[$forc][2] + 1, '_', $stateurl[$state], '.png"><br/><br/>';
      $i += $forecast[$forc][2];
    }
  }

  include('tail.php');
  ?>
</body>

</html>