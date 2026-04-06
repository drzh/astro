<!DOCTYPE html>
<html>
<?php include("../head.php") ?>
<body>
<?php
require '../menu.php';
include_once '../libtable.php';

$fi = 'freq.csv';
$headers = array('Satellite', 'ID', 'Uplink', 'Downlink', 'Beacon', 'Mode', 'Callsign', 'Status');
$rows = array();
$sort_values = array();

$fh = fopen($fi, 'r') or die("Cannot open file $fi!\n");
while (($row = fgetcsv($fh, 1000, ';')) !== false) {
  if (!array_key_exists(7, $row) || trim((string) $row[7]) !== 'active') {
    continue;
  }

  $display_row = array();
  $sort_row = array();
  foreach (range(0, 7) as $i) {
    $value = $row[$i] ?? '';
    $display_row[] = htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    $sort_row[] = trim((string) $value);
  }
  $rows[] = $display_row;
  $sort_values[] = $sort_row;
}
fclose($fh);

echo '<section class="panel">';
echo '<div class="chip-row"><span class="page-toolbar__label">Satellite Frequencies</span></div>';
render_sortable_table($headers, $rows, $sort_values, array('empty_message' => 'No active satellite frequencies were found.'));
echo '</section>';

include('../tail.php');
?>
</body>
</html>
