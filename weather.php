<?php
$state = 'all';
if (isset($_GET['st']) && $_GET['st'] !== '') {
  $state = $_GET['st'];
}

$pagewt = 'index.php';
$states = array_values(array_unique(array_filter(array_column($pos, 7), function ($value) {
  return $value !== 'any' && $value !== '';
})));
sort($states);

echo '<section class="panel">';
echo '<div class="filter-bar">';
echo '<span class="filter-label">State</span>';
echo '<div class="filter-links">';
echo '<a class="menu-state-link', ($state === 'all' ? ' is-active' : ''), '" href="', $pagewt, '?st=all">All</a>';
foreach ($states as $s) {
  echo '<a class="menu-state-link', ($state === $s ? ' is-active' : ''), '" href="', $pagewt, '?st=', urlencode($s), '">', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'), '</a>';
}
echo '</div>';
echo '</div>';
echo '</section>';

echo '<div class="weather-stack">';
foreach ($pos as $p) {
  if ($state !== 'all' && $p[7] !== 'any' && $p[7] !== $state) {
    continue;
  }

  $ran = rand(1, 1000000);
  $coord_link = '';
  if ($p[2] <= 360 && $p[1] <= 90) {
    $coord_link = 'https://maps.google.com/maps?q=' . $p[1] . ',' . $p[2];
  }

  echo '<section class="weather-card weather-card--compact panel">';
  echo '<div class="weather-card__header weather-card__header--compact">';
  echo '<h2 class="weather-card__title weather-card__title--compact"><a href="', htmlspecialchars($p[4], ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener noreferrer">', htmlspecialchars($p[0], ENT_QUOTES, 'UTF-8'), '</a></h2>';
  echo '<div class="weather-card__meta weather-card__meta--compact">';
  if ($p[7] !== '') {
    echo '<span class="weather-card__badge">', htmlspecialchars($p[7], ENT_QUOTES, 'UTF-8'), '</span>';
  }
  if ($coord_link !== '') {
    if ($p[7] !== '') {
      echo '<span class="weather-card__dot" aria-hidden="true">&bull;</span>';
    }
    echo '<a href="', htmlspecialchars($coord_link, ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener noreferrer">', htmlspecialchars($p[1] . ', ' . $p[2], ENT_QUOTES, 'UTF-8'), '</a>';
  }
  echo '</div>';
  echo '</div>';

  echo '<div class="weather-card__grid">';
  if ($p[3] !== '') {
    echo '<figure class="media-panel">';
    echo '<span class="media-panel__label">Clear Dark Sky</span>';
    echo '<img src="', htmlspecialchars($p[3], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Clear Dark Sky chart for ', htmlspecialchars($p[0], ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async">';
    echo '</figure>';
  }

  if ($coord_link !== '') {
    echo '<figure class="media-panel">';
    echo '<span class="media-panel__label">7Timer Astro Forecast</span>';
    echo '<img src="https://www.7timer.info/bin/astro.php?lon=', htmlspecialchars($p[2], ENT_QUOTES, 'UTF-8'), '&lat=', htmlspecialchars($p[1], ENT_QUOTES, 'UTF-8'), '&lang=en&ac=0&unit=metric&output=internal&tzshift=0&v=', $ran, '" alt="7Timer astro forecast for ', htmlspecialchars($p[0], ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async">';
    echo '</figure>';
  }
  echo '</div>';

  if ($p[6] !== '') {
    $ahour = preg_replace('/ahour=0/', 'ahour=48', $p[6]);
    echo '<div class="media-grid-two">';
    echo '<figure class="media-panel">';
    echo '<span class="media-panel__label">NOAA Meteogram 0-48h</span>';
    echo '<img src="', htmlspecialchars($p[6], ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="NOAA meteogram for ', htmlspecialchars($p[0], ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async">';
    echo '</figure>';
    echo '<figure class="media-panel">';
    echo '<span class="media-panel__label">NOAA Meteogram 48-96h</span>';
    echo '<img src="', htmlspecialchars($ahour, ENT_QUOTES, 'UTF-8'), '?=', $ran, '" alt="Extended NOAA meteogram for ', htmlspecialchars($p[0], ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async">';
    echo '</figure>';
    echo '</div>';
  }

  echo '</section>';
}
echo '</div>';
?>
