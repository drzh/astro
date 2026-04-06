<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>

<body>
<?php
    $datadir = '/home/celaeno/web/astro/nam';
    $f1 = $datadir . '/all.skycover.84hr.UTC.format';
    $f2 = '';
    $f3 = '';

    require_once __DIR__ . '/includes/sites.php';
    $pos = astro_load_site_data();
    require 'menu.php';
    include 'plot_weather.php';
    include 'tail.php';
?>
</body>

</html>
