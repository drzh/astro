<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
<?php
require 'menu.php';

$sat = trim((string) ($_GET['sat'] ?? ''));
$satellite_files = array();
$selected_html = '';

foreach (glob('satellite/data/sat.*.table*.html') as $f) {
    $e = explode('.', basename($f));
    if (array_key_exists(1, $e)) {
        $satellite_files[$e[1]] = $f;
    }
}
ksort($satellite_files);

if ($sat !== '' && array_key_exists($sat, $satellite_files)) {
    ob_start();
    include $satellite_files[$sat];
    $selected_html = ob_get_clean();
}

echo '<div class="split-layout">';
echo '<aside class="panel page-sidebar">';
if (!empty($satellite_files)) {
    echo '<div class="menu-stack menu-stack--column">';
    foreach ($satellite_files as $name => $path) {
        if ($sat === $name) {
            echo '<div class="citem">', htmlspecialchars($name, ENT_QUOTES, 'UTF-8'), '</div>';
        } else {
            echo '<a class="menu-state-link" href="satellite.html.php?sat=', urlencode($name), '">', htmlspecialchars($name, ENT_QUOTES, 'UTF-8'), '</a>';
        }
    }
    echo '</div>';
} else {
    echo '<p class="page-note">No embedded satellite HTML tables were found.</p>';
}
echo '</aside>';

echo '<section class="panel">';
if ($selected_html !== '') {
    echo '<div class="chip-row"><span class="page-toolbar__label">Satellite: ', htmlspecialchars($sat, ENT_QUOTES, 'UTF-8'), '</span></div>';
    echo '<div class="legacy-html-panel">', $selected_html, '</div>';
} elseif ($sat !== '') {
    echo '<p class="page-note">No embedded HTML pass table was found for ', htmlspecialchars($sat, ENT_QUOTES, 'UTF-8'), '.</p>';
} elseif (!empty($satellite_files)) {
    echo '<p class="page-note">Choose a satellite from the list to view the embedded pass table.</p>';
} else {
    echo '<p class="page-note">This page is ready, but there are currently no embedded satellite HTML tables to display.</p>';
}
echo '</section>';
echo '</div>';

include 'tail.php';
?>
</body>
</html>
