<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';

$st = '';
$ch = '';
$tg = '';
if (isset($_GET['st'])) {
  $st = $_GET['st'];
}
if (isset($_GET['ch'])) {
  $ch = $_GET['ch'];
}
if (isset($_GET['tg'])) {
  $ch = $_GET['tg'];
}

$fs = glob('goes/data/*.[0-9][0-9].png');

$state = array();
foreach ($fs as $f) {
  $e = explode('.', basename($f));
  if (! array_key_exists($e[0], $state)) {
    $state[$e[0]] = array();
  }
  array_push($state[$e[0]], $e[1]);
  #echo $f, '-', $e[0], '-', $e[1], '<br/>';
}

foreach (array_keys($state) as $s) {
  echo $s, ' : ', '<a href="goes.php?st=', $s, '&ch=All">All</a>';
  foreach (array_values($state[$s]) as $v) {
    echo ' - <a href="goes.php?st=', $s,'&ch=', $v, '">', $v, '</a>';
  }
  echo '<br/>';
}

if ($st != '' && $ch != '') {
  $chs = array();
  if ($ch == 'All') {
    $chs = $state[$st];
  }
  else {
    $chs = array($ch);
  }
  foreach ($chs as $c) {
    $fname = $st . '.' . $c . '.png';
    $rand = rand(100, 999);
    echo $st, ' ', $c, '<br/>';
    echo '<img src="goes/data/', $fname, '?=', $rand, '"><br/>';
  }
}

include('tail.php');
?>
</body>
</html>
