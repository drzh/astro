<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<?php require_once __DIR__ . '/includes/plot.php'; ?>
<?php include("nam_overlay_page.php") ?>
<body>
<script src="cloud.js"></script>
<?php
require 'menu.php';

$ranges = array();
for ($i = 2; $i < 41; $i += 4) {
    $range_end = min($i + 3, 41);
    $ranges[] = array(
        'bg' => $i,
        'ed' => $range_end,
        'label' => (string) (($i + 4 - 6) / 4),
    );
}

nam_render_overlay_page(array(
    'page' => 'nam240p.php',
    'selector_label' => 'Day',
    'ranges' => $ranges,
    'projection_file' => 'site/site.nam240.proj',
    'path_template' => 'site/path.%s.nam240.proj',
    'image_type' => 'NAM',
    'region' => 'NAM240',
    'frame_title_callback' => function ($i) {
        return 'Forecast Step ' . $i;
    },
    'image_url_callback' => function ($i, $ran) {
        return 'https://weatherstreet.com/gfs_files/gfs_clouds_us_' . $i . '.png?=' . $ran;
    },
));

include('tail.php');
?>
</body>
</html>
