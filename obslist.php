<!DOCTYPE html>
<html>
<?php include "head.php"; ?>
<body>
<?php
require 'menu.php';
require_once __DIR__ . '/includes/table.php';

$st = '';
if (isset($_GET['st'])) {
    $st = $_GET['st'];
}

$files = glob('list/data/*.csv');
$pagewt = basename($_SERVER['PHP_SELF'] ?? 'obslist.php');
$headers = array();
$rows = array();

ob_start();
echo '<div class="menu-stack menu-stack--column">';
foreach ($files as $f) {
    $e = explode('.', basename($f));
    $s = $e[0];
    if ($st == $s) {
        echo '<div class="citem">', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'), '</div>';
    } else {
        echo '<a class="menu-state-link" href="', htmlspecialchars($pagewt, ENT_QUOTES, 'UTF-8'), '?st=', urlencode($s), '">', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'), '</a>';
    }
}
echo '</div>';
$sidebar_menu = ob_get_clean();

if ($st != '') {
    $fname = 'list/data/' . $st . '.csv';
    if (file_exists($fname)) {
        $fh = fopen($fname, 'r') or die("Cannot open file $fname!\n");
        while (($e = fgetcsv($fh)) !== false) {
            if (empty($headers)) {
                $headers = $e;
            } else {
                $rows[] = $e;
            }
        }
        fclose($fh);
    }
}

echo '<div class="split-layout">';
echo '<aside class="panel page-sidebar">';
echo $sidebar_menu;
echo '</aside>';

echo '<section class="panel">';
if ($st !== '') {
    echo '<div class="chip-row"><span class="page-toolbar__label">List: ', htmlspecialchars($st, ENT_QUOTES, 'UTF-8'), '</span></div>';
}
if (!empty($headers)) {
    render_sortable_table($headers, $rows, array(), array('empty_message' => 'No rows were found in this list.'));
} else {
    echo '<p class="page-note">Choose a list from the sidebar to view the table.</p>';
}
echo '</section>';
echo '</div>';

include 'tail.php';
?>
</body>
</html>
