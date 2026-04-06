<!DOCTYPE html>
<html>
<?php include "head.php"; ?>
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
    $begin = (int) $_GET['bg'];
}
if (isset($_GET['ed'])) {
    $end = (int) $_GET['ed'];
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
$stateurl = array(
    'US' => 'conus',
    'TX' => 'southplains'
);

echo '<section class="panel">';
echo '<div class="menu-stack">';
foreach (array_keys($forecast) as $fc) {
    $href = '/ndfd.php?fc=' . urlencode($fc);
    if ($begin >= 0) {
        $href .= '&st=' . urlencode($state) . '&bg=' . $begin . '&ed=' . $end;
    }
    echo astro_nav_item($href, $forecast[$fc][0], $forc == $fc);
}
echo '</div>';

foreach (array_keys($stateurl) as $st) {
    echo '<div class="page-toolbar">';
    echo '<span class="page-toolbar__label">', htmlspecialchars($st, ENT_QUOTES, 'UTF-8'), '</span>';
    echo '<div class="chip-row">';
    for ($i = 0; $i < 165; $i += 24) {
        $label = (string) ($i / 24);
        $href = 'ndfd.php?fc=' . urlencode($forc) . '&st=' . urlencode($st) . '&bg=' . $i . '&ed=' . ($i + 23);
        echo astro_nav_item($href, $label, $state == $st && $begin == $i && $end == $i + 23);
    }
    echo '</div>';
    echo '</div>';
}
echo '</section>';

if ($begin >= 0 && $state != '' && array_key_exists($forc, $forecast)) {
    echo '<div class="weather-stack">';
    for ($i = $begin; $i <= $end; $i += $forecast[$forc][2]) {
        $pic_id = $i / $forecast[$forc][2] + 1;
        if ($pic_id < 25 || $pic_id % 2 == 1) {
            $image = $forecast[$forc][1] . $stateurl[$state] . '/' . $forc . $pic_id . '_' . $stateurl[$state] . '.png';
            echo '<section class="panel">';
            echo '<h2 class="panel-title">', htmlspecialchars($forecast[$forc][0], ENT_QUOTES, 'UTF-8'), ' Day ', htmlspecialchars((string) ($i / 24), ENT_QUOTES, 'UTF-8'), '</h2>';
            echo '<figure class="media-panel"><img src="', htmlspecialchars($image, ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($forecast[$forc][0], ENT_QUOTES, 'UTF-8'), ' forecast image ', htmlspecialchars((string) $pic_id, ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async"></figure>';
            echo '</section>';
        }
    }
    echo '</div>';
} else {
    echo '<section class="panel"><p class="page-note">Choose a forecast product and time range to display the current NDFD graphics.</p></section>';
}

include 'tail.php';
?>
</body>
</html>
