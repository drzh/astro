<?php
$page_title = 'Atmospheric Optics';
require_once __DIR__ . '/includes/table_modules.php';
$atmos_optics_module = astro_load_table_module('atmos_optics', $_GET);
?>
<!DOCTYPE html>
<html>
<?php include __DIR__ . '/head.php'; ?>
<body>
<?php
include __DIR__ . '/menu.php';
astro_render_table_module($atmos_optics_module);
include __DIR__ . '/tail.php';
?>
</body>
</html>
