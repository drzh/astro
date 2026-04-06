<!DOCTYPE html>
<html>
<?php include('head.php') ?>
<body>
<?php
require 'menu.php';

function goes_nav_item($href, $label, $active)
{
  if ($active) {
    return '<span class="citem">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
  }

  return '<a class="menu-state-link" href="' . $href . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';
}

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
  $tg = $_GET['tg'];
}
if ($tg !== '') {
  $ch = $tg;
}

$fs = glob('goes/data/*.[0-9][0-9].png');
$state = array();
foreach ($fs as $f) {
  $e = explode('.', basename($f));
  if (!array_key_exists($e[0], $state)) {
    $state[$e[0]] = array();
  }
  $state[$e[0]][] = $e[1];
}
ksort($state);

echo '<section class="panel">';
foreach (array_keys($state) as $s) {
  echo '<div class="page-toolbar">';
  echo '<span class="page-toolbar__label">', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'), '</span>';
  echo '<div class="chip-row">';
  echo goes_nav_item('goes.php?st=' . urlencode($s) . '&ch=All', 'All', $st == $s && $ch == 'All');
  foreach (array_values($state[$s]) as $v) {
    echo goes_nav_item('goes.php?st=' . urlencode($s) . '&ch=' . urlencode($v), $v, $st == $s && $ch == $v);
  }
  echo '</div>';
  echo '</div>';
}
echo '</section>';

if ($st != '' && $ch != '') {
  $chs = $ch == 'All' ? $state[$st] : array($ch);
  foreach ($chs as $c) {
    $fname = $st . '.' . $c . '.png';
    $rand = rand(100, 999);
    echo '<section class="panel">';
    echo '<h2 class="panel-title">', htmlspecialchars($st . ' ' . $c, ENT_QUOTES, 'UTF-8'), '</h2>';
    echo '<figure class="media-panel">';
    echo '<img src="goes/data/', htmlspecialchars($fname, ENT_QUOTES, 'UTF-8'), '?=', $rand, '" alt="', htmlspecialchars($st . ' ' . $c . ' GOES image', ENT_QUOTES, 'UTF-8'), '">';
    echo '</figure>';
    echo '</section>';
  }
}

include('tail.php');
?>
</body>
</html>
