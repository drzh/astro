<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
<?php
require_once __DIR__ . '/includes/sites.php';

$pos = astro_load_site_data();

include 'menu.php';
include 'daynighttime.php';
include 'tail.php';
?>
</body>
</html>
