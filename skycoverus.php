<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
<?php
require 'menu.php';

$para_st = '';
if (isset($_GET['st'])) {
    $para_st = $_GET['st'];
}
$para_day = '';
if (isset($_GET['day'])) {
    $para_day = $_GET['day'];
}

$fs = glob('skycover/img/*.png');
$state = array();
foreach ($fs as $f) {
    $e = explode('.', basename($f));
    if (!array_key_exists($e[0], $state)) {
        $state[$e[0]] = array();
    }
    $state[$e[0]][] = $e[1];
}
ksort($state);

echo '<section class="panel">';
foreach (array_keys($state) as $s) {
    echo '<div class="page-toolbar">';
    echo '<span class="page-toolbar__label">', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'), '</span>';
    echo '<div class="chip-row">';
    $daypre = '';
    foreach (array_values($state[$s]) as $t) {
        $time = strtotime($t . '00 UTC');
        $daytag = date('D, n/j', $time);
        $day = date('n/j', $time);
        if ($daypre != $day) {
            echo astro_nav_item('skycoverus.php?st=' . urlencode($s) . '&day=' . urlencode($day), $daytag, $s == $para_st && $day == $para_day);
            $daypre = $day;
        }
    }
    echo '</div>';
    echo '</div>';
}
echo '</section>';

$selected_any = false;
foreach ($fs as $f) {
    $e = explode('.', basename($f));
    $s = $e[0];
    $t = strtotime($e[1] . '00 UTC');
    $day = date('n/j', $t);
    if ($s == $para_st && $day == $para_day) {
        $selected_any = true;
        echo '<section class="panel">';
        echo '<h2 class="panel-title">', htmlspecialchars(date('D, n/j, H:i', $t), ENT_QUOTES, 'UTF-8'), '</h2>';
        echo '<div class="media-grid-two">';
        echo '<figure class="media-panel"><img src="', htmlspecialchars($f, ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($s . ' sky cover ' . date('D, n/j, H:i', $t), ENT_QUOTES, 'UTF-8'), '"></figure>';
        echo '<figure class="media-panel"><span class="media-panel__label">Legend</span><img src="skycover/legend.png" alt="Sky cover legend"></figure>';
        echo '</div>';
        echo '</section>';
    }
}

if (!$selected_any) {
    echo '<section class="panel"><p class="page-note">Choose a region and day to display the available SkyCoverUS imagery.</p></section>';
}

include 'tail.php';
?>
</body>
</html>
