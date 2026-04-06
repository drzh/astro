<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<?php include("libplot.php") ?>
<?php include("nam_overlay_page.php") ?>
<body>
<script src="cloud.js"></script>
<?php
require 'menu.php';

$ranges = array();
for ($i = 1; $i < 128; $i += 8) {
    $range_end = min($i + 7, 128);
    $ranges[] = array(
        'bg' => $i,
        'ed' => $range_end,
        'label' => (string) (((($i + 8 - 1) * 3) / 24) - 1),
    );
}

nam_render_overlay_page(array(
    'page' => 'nam384p.php',
    'selector_label' => 'Day',
    'ranges' => $ranges,
    'projection_file' => 'site/site.nam384.proj',
    'path_template' => 'site/path.%s.nam384.proj',
    'image_type' => 'NAM',
    'region' => 'NAM384',
    'frame_title_callback' => function ($i) {
        return 'Hour ' . ($i * 3);
    },
    'image_url_callback' => function ($i, $ran) {
        return 'nam/png.384/current/GFSUS_prec_cloud_' . sprintf('%03d', $i * 3) . '.png?=' . $ran;
    },
));

include('tail.php');
?>
</body>
</html>
