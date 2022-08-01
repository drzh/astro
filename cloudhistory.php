<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<script src="cloud.js">
</script>
<?php
require 'menu.php';

$mo = '';
if (isset($_GET['mo'])) {
  $mo = $_GET['mo'];
}

$months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', 'Annual');

echo '<a href="cloudhistory.php?mo=all">All</a>';
foreach ($months as $m) {
  echo ' - <a href="cloudhistory.php?mo=', $m,'">', $m, '</a>';
}
echo '<br />';

if ($mo != '') {
  $ms = array();
  if ($mo == 'all') {
    $ms = $months;
  }
  else {
    $ms = array($mo);
  }
  foreach ($ms as $m) {
    echo '<h2>', $m, '</h2>';
    echo '<img src="cloudhistory/', $m, '.png">';
  }
}

include('tail.php');
?>
</body>
</html>
