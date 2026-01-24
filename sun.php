<!DOCTYPE html>
<html>
<?php include("head.php"); ?>
<body>
<?php include('menu.php'); ?>
<table>
<tr style='vertical-align:top'><td>
<?php
# Get the 'intensity_max' from 'AIAsynoptic0304.full.txt'
$fi = 'sun/AIAsynoptic0304.full.txt';
$intensity_max = null;
if (file_exists($fi)) {
    $lines = file($fi);
    foreach ($lines as $line) {
        if (strpos($line, 'intensity_max') !== false) {
            $parts = preg_split('/\s+/', trim($line));
            if (count($parts) >= 2) {
                $intensity_max = $parts[1];
            }
            break;
        }
    }
}

$imgurl = [
    'AIA 304 | <a href="/sun/AIAsynoptic0304.full.txt">Max: ' . $intensity_max . '</a>' => ['sun/AIAsynoptic0304.full.png', 'sun/AIAsynoptic0304.full.png', 256, 256],
    'GOES-19 SUVI - AIA 304 &Aring / 50,000 K / Transition region / Chromosphere' => ['https://services.swpc.noaa.gov/images/animations/suvi/primary/304/latest.png', 'https://services.swpc.noaa.gov/images/animations/suvi/primary/304/latest.png', 256, 256],
    'HMI Continuum' => ['https://soho.nascom.nasa.gov/data/realtime/hmi_igr/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/hmi_igr/1024/latest.jpg', 256, 256],
    #'AIA 304 &Aring / 50,000 K / Transition region / Chromosphere' => ['https://soho.nascom.nasa.gov/data/realtime/eit_304/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/eit_304/1024/latest.jpg', 256, 256],
    'GOES19 CCOR-1' => ['https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg', 'https://services.swpc.noaa.gov/images/animations/ccor1/latest.jpg', 256, 256],
    'LASCO C2' => ['https://soho.nascom.nasa.gov/data/realtime/c2/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c2/1024/latest.jpg', 256, 256],
    'LASCO C3' => ['https://soho.nascom.nasa.gov/data/realtime/c3/512/latest.jpg', 'https://soho.nascom.nasa.gov/data/realtime/c3/1024/latest.jpg', 256, 256],
    'SDO Farside Image' => ['http://jsoc.stanford.edu/data/farside/Recent/Composite_Map.png', 'http://jsoc.stanford.edu/data/farside/Recent/Composite_Map.png', 450, 175],
    'Solar Cycle Sunspot Number Progression' => ['https://helioforecast.space//static/sync/icme_solar_cycle/cycle25_prediction_focus.png', 'https://helioforecast.space//static/sync/icme_solar_cycle/cycle25_prediction_focus.png', 450, 225],
];

$ran = rand(1,1000);

foreach ($imgurl as $title => $url) {
    echo "<h2>$title</h2>";
    echo "<a href='$url[1]?=$ran' target='_blank'><img width='$url[2]' height='$url[3]' src='$url[0]?=$ran' alt='$title' /></a>";
}

?>
</td></tr>
</table>
<?php include('tail.php'); ?>
</body>
</html>
