<?php
$current_page = basename($_SERVER['PHP_SELF'] ?? '');
$planet_links = array(
  'planets.php' => 'All',
  'planets_rise_set.php' => 'RiseSet',
  'jupiter.php' => 'Jupiter',
);

echo '<div class="chip-row">';
foreach ($planet_links as $path => $label) {
  if ($current_page === $path) {
    echo '<span class="citem">', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'), '</span>';
  } else {
    echo '<a class="menu-state-link" href="/', htmlspecialchars($path, ENT_QUOTES, 'UTF-8'), '">', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'), '</a>';
  }
}
echo '<span class="del">Saturn</span>';
echo '</div>';
?>
