<!DOCTYPE html>
<html>
<?php include("../head.php") ?>
<body>
<?php
require '../menu.php';

$fi = ('freq.csv');
$n = 0;
$rec = array();
$head = array();

# Push head line
$row = array('Satellite', 'ID', 'Uplink', 'Downlink', 'Beacon', 'Mode', 'Callsign', 'Status');
array_push($rec, $row);

$fh = fopen($fi, "r") or die("Cannot open file $fi!\n");
while(! feof($fh)) {
  if ($row = fgetcsv($fh, 1000, ';')) {
    array_push($rec, $row);
  }
}

/* usort($rec, 'cmp'); */
$n = 0;
/* array_unshift($rec, $head); */
echo '<table bgcolor="gray" cellspacing="1" cellpadding="3">';
foreach ($rec as $row) {
  if ($n == 0 || array_key_exists(7, $row) && $row[7] == 'active') {
    if ($n % 2 == 0) {
      echo '<tr bgcolor="#DDDDDD">';
    }
    else {
      echo '<tr bgcolor="#FFFFFF">';
    }
    foreach (range(0, 7, 1) as $i) {
      echo '<td align="center">';
      if (array_key_exists($i, $row)) {
        echo $row[$i];
      }
      echo '</td>';
    }
    echo '</tr>';
    $n++;
  }
}
echo '</td></tr></table>';

function cmp ($a, $b) {
  if ($a[0] == $b[0]) {
    return 0;
  }
  return ($a[0] < $b[0]) ? -1 : 1;
}

?>
</body>
</html>
