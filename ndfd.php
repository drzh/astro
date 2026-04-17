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
$selected_range = '';
if (isset($_GET['fc'])) {
    $forc = $_GET['fc'];
}
if (isset($_GET['st'])) {
    $state = $_GET['st'];
}
if (isset($_GET['range'])) {
    $selected_range = trim((string) $_GET['range']);
    if (preg_match('/^(\d+)-(\d+)$/', $selected_range, $matches)) {
        $begin = (int) $matches[1];
        $end = (int) $matches[2];
    }
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
$range_options = array();
for ($i = 0; $i < 165; $i += 24) {
    $range_options[] = array(
        'bg' => $i,
        'ed' => $i + 23,
        'label' => (string) ($i / 24),
    );
}

if (!array_key_exists($forc, $forecast)) {
    $forc = 'Wx';
}
if (!array_key_exists($state, $stateurl)) {
    $state_keys = array_keys($stateurl);
    $state = $state_keys[0];
}
$category_options = array();
foreach ($forecast as $fc => $fc_config) {
    $category_options[$fc] = $fc_config[0];
}
$region_options = array_combine(array_keys($stateurl), array_keys($stateurl));
$day_options = array();
foreach ($range_options as $range_option) {
    $day_options[(string) $range_option['bg'] . '-' . (string) $range_option['ed']] = $range_option['label'];
}

$active_range = null;
foreach ($range_options as $range_option) {
    if ($begin === $range_option['bg'] && $end === $range_option['ed']) {
        $active_range = $range_option;
        break;
    }
}
if ($active_range === null && $range_options !== array()) {
    $active_range = $range_options[0];
    $begin = $active_range['bg'];
    $end = $active_range['ed'];
}

echo '<section class="panel">';
echo '<form class="filter-form filter-form--compact" method="get" action="ndfd.php">';
echo astro_inline_select_field('ndfd-category', 'fc', 'Category', $category_options, $forc);
echo astro_inline_select_field('ndfd-region', 'st', 'Region', $region_options, $state);
echo astro_inline_select_field('ndfd-range-select', 'range', 'Day', $day_options, (string) $active_range['bg'] . '-' . (string) $active_range['ed']);
echo '<noscript><button class="filter-submit" type="submit">Apply</button></noscript>';
echo '</form>';
echo '</section>';

if ($begin >= 0 && $state != '' && array_key_exists($forc, $forecast)) {
    echo '<div class="weather-stack">';
    for ($i = $begin; $i <= $end; $i += $forecast[$forc][2]) {
        $pic_id = $i / $forecast[$forc][2] + 1;
        if ($pic_id < 25 || $pic_id % 2 == 1) {
            $image = $forecast[$forc][1] . $stateurl[$state] . '/' . $forc . $pic_id . '_' . $stateurl[$state] . '.png';
            echo '<section class="panel">';
            echo '<h2 class="panel-title">', htmlspecialchars($forecast[$forc][0], ENT_QUOTES, 'UTF-8'), ' Day ', htmlspecialchars((string) ($i / 24), ENT_QUOTES, 'UTF-8'), '</h2>';
            echo '<figure class="media-panel"><img class="media-panel__image--intrinsic" src="', htmlspecialchars($image, ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($forecast[$forc][0], ENT_QUOTES, 'UTF-8'), ' forecast image ', htmlspecialchars((string) $pic_id, ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async"></figure>';
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
