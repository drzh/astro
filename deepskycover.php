<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';

$fs = glob('deepskycover/img/*h.png');

foreach ($fs as $f) {
  $e = explode('.', basename($f));
  $ran = rand(1,1000000);
  echo '<h2>', $e[0], '</h2>', "\n";
  echo '<img src="', $f, '?=', $ran, '">', "\n";
  echo '<hr>', "\n";
}

include('tail.php');
?>
</body>
</html>
