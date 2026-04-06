<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';

$begin = -1;
$end = -1;
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

echo '<section class="panel">';
echo '<div class="page-toolbar">';
echo '<span class="page-toolbar__label">Hour</span>';
echo '<div class="chip-row">';
for ($i = 1; $i < 29; $i += 3) {
    $range_end = min($i + 2, 28);
    $label = (($i - 1) * 3) . '-' . (($range_end - 1) * 3);
    $active = ($begin === $i && $end === $range_end);
    $href = 'nam_old.php?bg=' . $i . '&ed=' . $range_end;
    if ($active) {
        echo '<span class="citem">', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'), '</span>';
    } else {
        echo '<a class="menu-state-link" href="', htmlspecialchars($href, ENT_QUOTES, 'UTF-8'), '">', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'), '</a>';
    }
}
echo '</div>';
echo '</div>';
echo '</section>';

if ($begin > 0) {
    echo '<div class="weather-stack">';
    for ($i = $begin; $i <= $end; $i++) {
        echo '<section class="panel">';
        echo '<h2 class="panel-title">Hour ', htmlspecialchars((string) (($i - 1) * 3), ENT_QUOTES, 'UTF-8'), '</h2>';
        echo '<figure class="media-panel image-scroll">';
        echo '<img src="http://ready.arl.noaa.gov/data/forecast/grads/nam/panel10/plt', htmlspecialchars((string) $i, ENT_QUOTES, 'UTF-8'), '.gif" alt="Legacy NAM forecast hour ', htmlspecialchars((string) (($i - 1) * 3), ENT_QUOTES, 'UTF-8'), '">';
        echo '</figure>';
        echo '</section>';
    }
    echo '</div>';
}

include('tail.php');
?>
</body>
</html>
