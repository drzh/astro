<!DOCTYPE html>
<html>
<?php include "head.php"; ?>
<body>
<?php
require 'menu.php';

$sfa = array(
    array(
        'Dallas - Gamma Exposure Rate in the unit of milliRoentgen per hour (mR/h)',
        'https://www3.epa.gov/radnet00/images/exp/dallas-exp.jpg'
    ),
    array(
        'Fort Worth - Gamma Exposure Rate in the unit of milliRoentgen per hour (mR/h)',
        'https://www3.epa.gov/radnet00/images/exp/ft.worth-exp.jpg',
    ),
);

foreach ($sfa as $c) {
        $ran = rand(1,1000);
        echo '<section class="panel">';
        echo '<h2 class="panel-title">', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), '</h2>';
        echo '<figure class="media-panel">';
        echo '<img src="', htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="', htmlspecialchars($c[0], ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async">';
        echo '</figure>';
        echo '</section>';
}

include 'tail.php';
?>
</body>
</html>
