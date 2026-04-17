<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
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
    echo '<section class="panel">';
    echo '<h2 class="panel-title">', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), '</h2>';
    echo '<figure class="media-panel">';
    echo '<img class="media-panel__image--intrinsic" src="', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async">';
    echo '</figure>';
    echo '</section>';
}

include 'tail.php';
?>
</body>
</html>
