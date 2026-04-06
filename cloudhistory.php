<!DOCTYPE html>
<html>
<?php include('head.php') ?>
<body>
<?php
require 'menu.php';

function cloudhistory_nav_item($href, $label, $active)
{
  if ($active) {
    return '<span class="citem">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
  }

  return '<a class="menu-state-link" href="' . $href . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';
}

$mo = '';
if (isset($_GET['mo']) && $_GET['mo'] !== '') {
  $mo = $_GET['mo'];
}

$months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', 'Annual');

echo '<section class="panel">';
echo '<div class="chip-row">';
echo cloudhistory_nav_item('cloudhistory.php?mo=all', 'All', $mo === 'all');
foreach ($months as $m) {
  echo cloudhistory_nav_item('cloudhistory.php?mo=' . urlencode($m), $m, $mo === $m);
}
echo '</div>';
echo '</section>';

if ($mo !== '') {
  $selected_months = $mo === 'all' ? $months : array($mo);
  echo '<div class="weather-stack">';
  foreach ($selected_months as $month) {
    echo '<section class="panel">';
    echo '<h2 class="panel-title">', htmlspecialchars($month, ENT_QUOTES, 'UTF-8'), '</h2>';
    echo '<figure class="media-panel">';
    echo '<img src="cloudhistory/', rawurlencode($month), '.png" alt="Cloud history chart for ', htmlspecialchars($month, ENT_QUOTES, 'UTF-8'), '">';
    echo '</figure>';
    echo '</section>';
  }
  echo '</div>';
} else {
  echo '<section class="panel"><p class="page-note">Choose a month above to load the archived chart.</p></section>';
}

include('tail.php');
?>
</body>
</html>
