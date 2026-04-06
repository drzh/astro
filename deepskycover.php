<!DOCTYPE html>
<html>
<?php include('head.php') ?>
<body>
<?php
require 'menu.php';

$fs = glob('deepskycover/img/*h.png');
sort($fs, SORT_NATURAL);

if (count($fs) > 0) {
  echo '<div class="weather-stack">';
  foreach ($fs as $f) {
    $label = pathinfo(basename($f), PATHINFO_FILENAME);
    $ran = rand(1, 1000000);
    echo '<section class="panel">';
    echo '<h2 class="panel-title">', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'), '</h2>';
    echo '<figure class="media-panel image-scroll">';
    echo '<img src="', htmlspecialchars($f, ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Deep sky cover image ', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'), '">';
    echo '</figure>';
    echo '</section>';
  }
  echo '</div>';
} else {
  echo '<section class="panel"><p class="page-note">No deep sky cover images were found.</p></section>';
}

include('tail.php');
?>
</body>
</html>
