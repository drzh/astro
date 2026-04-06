<!DOCTYPE html>
<html>
<?php include "../head.php"; ?>
<body>
<?php
require '../menu.php';
require_once __DIR__ . '/../includes/table.php';

$toggle = [
    'GOES' => 'tggoes',
    'SkyCover' => 'tgsky',
    'SkyCoverUS' => 'tgskyus',
    'SatVis_Alert' => 'tgsatvisalt',
    'SatHam_Alert' => 'tgsathamalt',
    'NAM-60' => 'tgnam60',
    'NAM-84' => 'tgnam84',
];

$headers = array('Feature', 'Status');
$rows = array();
$sort_values = array();

foreach ($toggle as $k => $v) {
    $tgf = $v . '.off';

    if (isset($_GET[$v])) {
        $st = $_GET[$v];
        if ($st == 'on' && file_exists($tgf)) {
            unlink($tgf);
        }
        if ($st == 'off' && (!file_exists($tgf))) {
            fopen($tgf, 'w') or die("Unable to open file: $tgf !");
        }
    }

    if (file_exists($tgf)) {
        $status_cell = '<a class="statoff" href="index.php?' . urlencode($v) . '=on">Off</a>';
        $status_sort = 'Off';
    } else {
        $status_cell = '<a class="staton" href="index.php?' . urlencode($v) . '=off">On</a>';
        $status_sort = 'On';
    }

    $rows[] = array(
        htmlspecialchars($k, ENT_QUOTES, 'UTF-8'),
        $status_cell,
    );
    $sort_values[] = array($k, $status_sort);
}

echo '<section class="panel">';
echo '<div class="chip-row"><span class="page-toolbar__label">Feature Toggles</span></div>';
render_sortable_table($headers, $rows, $sort_values);
echo '</section>';

include '../tail.php';
?>
</body>
</html>
