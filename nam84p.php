<!DOCTYPE html>
<html>
<?php include "head.php"; ?>
<?php require_once __DIR__ . '/includes/plot.php'; ?>
<?php include "nam_overlay_page.php"; ?>
<body>
<script src="cloud.js"></script>
<?php
require 'menu.php';

$ranges = array();
for ($i = 1; $i <= 28; $i += 4) {
    $range_end = min($i + 3, 28);
    $ranges[] = array(
        'bg' => $i,
        'ed' => $range_end,
        'label' => ($i * 3) . '-' . ($range_end * 3),
    );
}

nam_render_overlay_page(array(
    'page' => 'nam84p.php',
    'selector_label' => 'Hour',
    'ranges' => $ranges,
    'projection_file' => 'site/site.nam84.proj',
    'path_template' => 'site/path.%s.nam84.proj',
    'image_type' => 'NAM',
    'region' => 'NAM84',
    'frame_title_callback' => function ($i) {
        return 'Hour ' . ($i * 3);
    },
    'image_url_callback' => function ($i, $ran) {
        return 'https://weatherstreet.com/nam_files/nam_mslp_pcpn_frzn_clouds_us_' . $i . '.png?=' . $ran;
    },
));

include 'tail.php';
?>
</body>
</html>
