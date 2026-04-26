<?php
$state = 'all';
if (isset($_GET['st']) && $_GET['st'] !== '') {
    $state = $_GET['st'];
}

$pagewt = 'index.php';
$states = array_values(array_unique(array_filter(array_column($pos, 'state'), function ($value) {
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
    $siteName = $p['name'];
    $latitude = $p['latitude'];
    $longitude = $p['longitude'];
    $clearDarkSkyImage = $p['clear_dark_sky_image'];
    $siteLink = $p['clear_dark_sky_link'];
    $meteogramUrl = $p['meteogram_url'];
    $siteState = $p['state'];

    if ($state !== 'all' && $siteState !== 'any' && $siteState !== $state) {
        continue;
    }

    $ran = rand(1, 1000000);
    $coord_link = '';
    if ($longitude <= 360 && $latitude <= 90) {
        $coord_link = 'https://maps.google.com/maps?q=' . $latitude . ',' . $longitude;
    }

    echo '<section class="weather-card weather-card--compact panel">';
    echo '<div class="weather-card__header weather-card__header--compact">';
    echo '<h2 class="weather-card__title weather-card__title--compact"><a class="weather-card__title-link--inline" href="', htmlspecialchars($siteLink, ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener noreferrer">', htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'), '</a>';
    echo '<span class="weather-card__meta weather-card__meta--compact weather-card__meta--inline">';
    if ($siteState !== '' || $coord_link !== '') {
        echo '<span class="weather-card__dot" aria-hidden="true">&bull;</span>';
    }
    if ($siteState !== '') {
        echo '<span class="weather-card__badge">', htmlspecialchars($siteState, ENT_QUOTES, 'UTF-8'), '</span>';
    }
    if ($coord_link !== '') {
        if ($siteState !== '') {
            echo '<span class="weather-card__dot" aria-hidden="true">&bull;</span>';
        }
        echo '<a href="', htmlspecialchars($coord_link, ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener noreferrer">', htmlspecialchars($latitude . ', ' . $longitude, ENT_QUOTES, 'UTF-8'), '</a>';
    }
    echo '</span></h2>';
    echo '</div>';

    echo '<div class="weather-card__grid">';
    if ($clearDarkSkyImage !== '') {
        $clearDarkSkySrc = $clearDarkSkyImage . '?=' . $ran;
        $clearDarkSkyAlt = 'Clear Dark Sky chart for ' . $siteName;
        $clearDarkSkyCaption = $siteName . ' | Clear Dark Sky';
        echo '<figure class="media-panel">';
        echo '<span class="media-panel__label">Clear Dark Sky</span>';
        echo '<a class="media-panel__trigger" data-image-modal href="', htmlspecialchars($clearDarkSkySrc, ENT_QUOTES, 'UTF-8'), '" data-image-modal-alt="', htmlspecialchars($clearDarkSkyAlt, ENT_QUOTES, 'UTF-8'), '" data-image-modal-caption="', htmlspecialchars($clearDarkSkyCaption, ENT_QUOTES, 'UTF-8'), '">';
        echo '<img src="', htmlspecialchars($clearDarkSkySrc, ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($clearDarkSkyAlt, ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async">';
        echo '</a>';
        echo '</figure>';
    }

    if ($coord_link !== '') {
        $timerSrc = 'https://www.7timer.info/bin/astro.php?lon=' . $longitude . '&lat=' . $latitude . '&lang=en&ac=0&unit=metric&output=internal&tzshift=0&v=' . $ran;
        $timerAlt = '7Timer astro forecast for ' . $siteName;
        $timerCaption = $siteName . ' | 7Timer Astro Forecast';
        echo '<figure class="media-panel">';
        echo '<span class="media-panel__label">7Timer Astro Forecast</span>';
        echo '<a class="media-panel__trigger" data-image-modal href="', htmlspecialchars($timerSrc, ENT_QUOTES, 'UTF-8'), '" data-image-modal-alt="', htmlspecialchars($timerAlt, ENT_QUOTES, 'UTF-8'), '" data-image-modal-caption="', htmlspecialchars($timerCaption, ENT_QUOTES, 'UTF-8'), '">';
        echo '<img src="', htmlspecialchars($timerSrc, ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($timerAlt, ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async">';
        echo '</a>';
        echo '</figure>';
    }
    echo '</div>';

    if ($meteogramUrl !== '') {
        $ahour = preg_replace('/ahour=0/', 'ahour=48', $meteogramUrl);
        $meteogramSrc = $meteogramUrl . '?=' . $ran;
        $meteogramAlt = 'NOAA meteogram for ' . $siteName;
        $meteogramCaption = $siteName . ' | NOAA Meteogram 0-48h';
        $meteogramExtendedSrc = $ahour . '?=' . $ran;
        $meteogramExtendedAlt = 'Extended NOAA meteogram for ' . $siteName;
        $meteogramExtendedCaption = $siteName . ' | NOAA Meteogram 48-96h';
        echo '<div class="media-grid-two">';
        echo '<figure class="media-panel">';
        echo '<span class="media-panel__label">NOAA Meteogram 0-48h</span>';
        echo '<a class="media-panel__trigger" data-image-modal href="', htmlspecialchars($meteogramSrc, ENT_QUOTES, 'UTF-8'), '" data-image-modal-alt="', htmlspecialchars($meteogramAlt, ENT_QUOTES, 'UTF-8'), '" data-image-modal-caption="', htmlspecialchars($meteogramCaption, ENT_QUOTES, 'UTF-8'), '">';
        echo '<img src="', htmlspecialchars($meteogramSrc, ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($meteogramAlt, ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async">';
        echo '</a>';
        echo '</figure>';
        echo '<figure class="media-panel">';
        echo '<span class="media-panel__label">NOAA Meteogram 48-96h</span>';
        echo '<a class="media-panel__trigger" data-image-modal href="', htmlspecialchars($meteogramExtendedSrc, ENT_QUOTES, 'UTF-8'), '" data-image-modal-alt="', htmlspecialchars($meteogramExtendedAlt, ENT_QUOTES, 'UTF-8'), '" data-image-modal-caption="', htmlspecialchars($meteogramExtendedCaption, ENT_QUOTES, 'UTF-8'), '">';
        echo '<img src="', htmlspecialchars($meteogramExtendedSrc, ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($meteogramExtendedAlt, ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async">';
        echo '</a>';
        echo '</figure>';
        echo '</div>';
    }

    echo '</section>';
}
echo '</div>';
?>
