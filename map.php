<!DOCTYPE html>
<html>
<?php include('head.php'); ?>
<body>
<?php include('menu.php'); ?>
<?php
$imgurl = [
    'World Magnetic Model - Epoch 2020.0 Geomagnetic Coordinates' => ['map/Geomagnetic_Coordinates.jpg', 'map/Geomagnetic_Coordinates.jpg', 777, 648],
    'World Magnetic Model - Epoch 2025.0 Main Field Declination (D)' => ['https://www.ncei.noaa.gov/sites/default/files/inline-images/D.jpg', 'https://www.ncei.noaa.gov/sites/default/files/inline-images/D.jpg', 691, 576],
    'World Magnetic Model - Epoch 2025.0 Main Field Inclination (I)' => ['https://www.ncei.noaa.gov/sites/default/files/inline-images/I.jpg', 'https://www.ncei.noaa.gov/sites/default/files/inline-images/I.jpg', 691, 576],
];

foreach ($imgurl as $title => $url) {
    echo '<section class="panel">';
    echo '<h2 class="panel-title">', $title, '</h2>';
    echo '<figure class="media-panel image-scroll">';
    echo '<a href="', $url[1], '" target="_blank" rel="noopener noreferrer"><img width="', $url[2], '" height="', $url[3], '" src="', $url[0], '" alt="', htmlspecialchars($title, ENT_QUOTES, 'UTF-8'), '" loading="lazy" decoding="async"></a>';
    echo '</figure>';
    echo '</section>';
}
?>
<?php include('tail.php'); ?>
</body>
</html>
