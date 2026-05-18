<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
<?php include 'menu.php'; ?>
<?php
$fi = 'sun/AIAsynoptic0304.full.txt';
$intensity_max = null;
$aia304_image = 'sun/AIAsynoptic0304.full.png';
$aia304_latlon_image = 'sun/AIAsynoptic0304.full.latlon.png';
$aia304_carrington_image = 'sun/AIAsynoptic0304.full.carrington.png';
$aia304_latlon_exists = file_exists(__DIR__ . '/' . $aia304_latlon_image);
$aia304_carrington_exists = file_exists(__DIR__ . '/' . $aia304_carrington_image);
$aia304_has_layout = $aia304_latlon_exists || $aia304_carrington_exists;
if (file_exists($fi)) {
    $lines = file($fi);
    foreach ($lines as $line) {
        if (strpos($line, 'intensity_max') !== false) {
            $parts = preg_split('/\s+/', trim($line));
            if (count($parts) >= 2) {
                $intensity_max = intval($parts[1]);
            }
            break;
        }
    }
}

$aia304_title = 'AIA 304';
if ($aia304_latlon_exists) {
    $aia304_title .= ' | <a href="#" data-aia304-toggle="latlon" data-aia304-state="off">Show Lat/Lon</a>';
}
if ($aia304_carrington_exists) {
    $aia304_title .= ' | <a href="#" data-aia304-toggle="carrington" data-aia304-state="off">Show Carrington</a>';
}
$aia304_title .= ' | <a href="/sun/AIAsynoptic0304.full.txt">Max: ' . $intensity_max . '</a> &bull; <a href="/table.php?tb=AIAsynoptic0304.hist.txt&amp;sort=obs_time&amp;order=desc">History</a>';
$aia304_title .= ' | <a href="https://www.swpc.noaa.gov/products/goes-solar-ultraviolet-imager-suvi" target="_blank" rel="noopener noreferrer">GOES SUVI</a>';

$imgurl = [
    [
        'id' => 'aia304',
        'title' => $aia304_title,
        'alt' => 'AIA 304 solar image',
        'src' => $aia304_image,
        'href' => $aia304_image,
        'width' => 256,
        'height' => 256,
    ],
    [
        'id' => 'goes304',
        'title' => 'GOES-19 SUVI - AIA 304 &Aring / 50,000 K / Transition region / Chromosphere',
        'alt' => 'GOES-19 SUVI AIA 304 image',
        'src' => 'https://services.swpc.noaa.gov/images/animations/suvi/primary/304/latest.png',
        'href' => 'https://services.swpc.noaa.gov/images/animations/suvi/primary/304/latest.png',
        'width' => 256,
        'height' => 256,
    ],
    [
        'id' => 'hmi',
        'title' => 'HMI Continuum',
        'alt' => 'HMI Continuum image',
        'src' => 'https://soho.nascom.nasa.gov/data/realtime/hmi_igr/512/latest.jpg',
        'href' => 'https://soho.nascom.nasa.gov/data/realtime/hmi_igr/1024/latest.jpg',
        'width' => 256,
        'height' => 256,
    ],
    [
        'id' => 'ccor1',
        'title' => 'GOES19 CCOR-1 | <a href="https://www.swpc.noaa.gov/products/ccor-1-coronagraph-experimental" target="_blank" rel="noopener noreferrer">Animation</a>',
        'alt' => 'GOES19 CCOR-1 image',
        'src' => 'https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg',
        'href' => 'https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg',
        'width' => 256,
        'height' => 256,
    ],
    [
        'id' => 'lasco-c2',
        'title' => 'LASCO C2 | <a href="https://www.swpc.noaa.gov/products/lasco-coronagraph" target="_blank" rel="noopener noreferrer">Animation</a>',
        'alt' => 'LASCO C2 image',
        'src' => 'https://soho.nascom.nasa.gov/data/realtime/c2/512/latest.jpg',
        'href' => 'https://soho.nascom.nasa.gov/data/realtime/c2/1024/latest.jpg',
        'width' => 256,
        'height' => 256,
    ],
    [
        'id' => 'lasco-c3',
        'title' => 'LASCO C3 | <a href="https://www.swpc.noaa.gov/products/lasco-coronagraph" target="_blank" rel="noopener noreferrer">Animation</a>',
        'alt' => 'LASCO C3 image',
        'src' => 'https://soho.nascom.nasa.gov/data/realtime/c3/512/latest.jpg',
        'href' => 'https://soho.nascom.nasa.gov/data/realtime/c3/1024/latest.jpg',
        'width' => 256,
        'height' => 256,
    ],
    [
        'id' => 'farside',
        'title' => 'SDO Farside Image',
        'alt' => 'SDO farside image',
        'src' => 'http://jsoc.stanford.edu/data/farside/Recent/Composite_Map.png',
        'href' => 'http://jsoc.stanford.edu/data/farside/Recent/Composite_Map.png',
        'width' => 450,
        'height' => 175,
    ],
    [
        'id' => 'solar_orbiter_phi',
        'title' => 'Solar Obiter PHI',
        'alt' => 'Solar Obiter PHI',
        'src' => 'https://www2.mps.mpg.de/projects/soho/sumer/phiimg/solo_LL02_phi-fdt-icnt_last.png',
        'href' => 'https://www2.mps.mpg.de/projects/soho/sumer/phiimg/solo_LL02_phi-fdt-icnt_last.png',
        'width' => 256,
        'height' => 256,
    ],
    [
        'id' => 'cycle25',
        'title' => 'Solar Cycle Sunspot Number Progression',
        'alt' => 'Solar cycle sunspot number progression chart',
        'src' => 'https://helioforecast.space//static/sync/icme_solar_cycle/cycle25_prediction_focus.png',
        'href' => 'https://helioforecast.space//static/sync/icme_solar_cycle/cycle25_prediction_focus.png',
        'width' => 450,
        'height' => 225,
    ],
];

$ran = rand(1, 1000);
$display_width = 512;

echo '<div class="weather-stack">';
foreach ($imgurl as $item) {
    $title = $item['title'];
    $image_src = $item['src'] . '?=' . $ran;
    $image_href = $item['href'] . '?=' . $ran;
    $image_alt = $item['alt'];
    $latlon_src = '';
    $carrington_src = '';

    if ($item['id'] === 'aia304' && $aia304_latlon_exists) {
        $latlon_src = $aia304_latlon_image . '?=' . $ran;
    }
    if ($item['id'] === 'aia304' && $aia304_carrington_exists) {
        $carrington_src = $aia304_carrington_image . '?=' . $ran;
    }

    echo '<section class="panel">';
    echo '<h2 class="panel-title">', $title, '</h2>';
    $figure_classes = 'media-panel media-panel--compact-graphic image-scroll';
    if ($item['id'] === 'aia304') {
        $figure_classes .= ' media-panel--align-start';
    }
    echo '<figure class="', $figure_classes, '">';
    if ($item['id'] === 'aia304' && $aia304_has_layout) {
        echo '<a class="sun-overlay-image" data-aia304-container style="--media-max-width:', $display_width, 'px;" href="', htmlspecialchars($image_href, ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener noreferrer">';
        echo '<img class="sun-overlay-image__base" width="', $item['width'], '" height="', $item['height'], '" src="', htmlspecialchars($image_src, ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($image_alt, ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async">';
        if ($latlon_src !== '') {
            echo '<img class="sun-overlay-image__overlay" width="', $item['width'], '" height="', $item['height'], '" src="', htmlspecialchars($latlon_src, ENT_QUOTES, 'UTF-8'), '" alt="" loading="lazy" decoding="async" data-aia304-overlay="latlon" aria-hidden="true">';
        }
        if ($carrington_src !== '') {
            echo '<img class="sun-overlay-image__overlay" width="', $item['width'], '" height="', $item['height'], '" src="', htmlspecialchars($carrington_src, ENT_QUOTES, 'UTF-8'), '" alt="" loading="lazy" decoding="async" data-aia304-overlay="carrington" aria-hidden="true">';
        }
        echo '</a>';
    } else {
        echo '<a href="', htmlspecialchars($image_href, ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener noreferrer"><img class="media-panel__image--scaled" style="--media-max-width:', $display_width, 'px;" width="', $item['width'], '" height="', $item['height'], '" src="', htmlspecialchars($image_src, ENT_QUOTES, 'UTF-8'), '" alt="', htmlspecialchars($image_alt, ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async"></a>';
    }
    echo '</figure>';
    echo '</section>';
}
echo '</div>';

if ($aia304_has_layout) {
    ?>
<script>
(function () {
    const labels = {
        latlon: 'Lat/Lon',
        carrington: 'Carrington',
    };

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-aia304-toggle]');
        if (!toggle) {
            return;
        }

        const type = toggle.dataset.aia304Toggle;
        const container = document.querySelector('[data-aia304-container]');
        const overlay = document.querySelector(`[data-aia304-overlay="${type}"]`);
        if (!container || !type || !overlay) {
            return;
        }

        event.preventDefault();
        const nextVisible = toggle.dataset.aia304State !== 'on';

        Object.keys(labels).forEach((overlayType) => {
            const layer = document.querySelector(`[data-aia304-overlay="${overlayType}"]`);
            const layerToggle = document.querySelector(`[data-aia304-toggle="${overlayType}"]`);
            container.classList.remove(`sun-overlay-image--${overlayType}-visible`);
            if (layer) {
                layer.setAttribute('aria-hidden', 'true');
            }
            if (layerToggle) {
                layerToggle.dataset.aia304State = 'off';
                layerToggle.textContent = `Show ${labels[overlayType]}`;
            }
        });

        if (nextVisible) {
            container.classList.add(`sun-overlay-image--${type}-visible`);
            overlay.setAttribute('aria-hidden', 'false');
            toggle.dataset.aia304State = 'on';
            toggle.textContent = `Hide ${labels[type] || type}`;
        }
    });
}());
</script>
<?php
}
?>
<?php include 'tail.php'; ?>
</body>
</html>
