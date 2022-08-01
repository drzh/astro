<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';

$sat = '';
if (isset($_GET['sat'])) {
  $sat = $_GET['sat'];
}

echo '<table><tr><td valign="top">';
$files = glob('satellite/data/sat.*.table*.html');
$n = 0;
foreach ($files as $f) {
  $e = explode('.', basename($f));
  $s = $e[1];
  echo '<a href="satellite.php?sat=', $s, '">', $s, '</a><br/>';
}
echo '</td><td valign="top">';

if ($sat != '') {
  $fname = 'satellite/data/sat.' . $sat . '.table.html';
  if (file_exists($fname)) {
    include($fname);
  }
}
echo '</td></tr></table>';

include('tail.php');
?>
</body>
</html>
