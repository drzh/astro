<!DOCTYPE html>
<html>
<?php include("head.php") ?>
<?php include("libplot.php") ?>

<body>
    <script src="cloud.js">
    </script>
    <?php
    require 'menu.php';

    $pa = '';
    if (isset($_GET['pa'])) {
        $pa = $_GET['pa'];
    }

    $begin = -1;
    $end = -1;
    if (isset($_GET['bg'])) {
        $begin = $_GET['bg'];
    }
    if (isset($_GET['ed'])) {
        $end = $_GET['ed'];
    }
    if ($begin > 0 && $end < 0) {
        $end = $begin;
    }
    if ($end > 0 && $begin < 0) {
        $begin = $end;
    }

    $fproj = 'site/site.nam240.proj';

    # Read the markers
    $marker = array();
    if ($fproj != '') {
        // read marker
        $i = 0;
        $fh = fopen($fproj, "r") or die("Cannot open file!\n");
        while (!feof($fh)) {
            if ($row = fgets($fh)) {
                $e = explode("\t", rtrim($row));
                array_push($marker, $e);
            }
        }
        fclose($fh);
    }

    # Read path files
    $paths = [];
    if ($pa != '') {
        $dir = 'site/';
        foreach (explode(',', $pa) as $p) {
            $fn =  $dir . 'path.' . $p . '.nam240.proj';
            if (file_exists($fn)) {
                $fh = fopen($fn, 'r') or die("Cannot open $fn\n");
                $path = [];
                while (!feof($fh)) {
                    if ($row = fgets($fh)) {
                        $e = explode("\t", rtrim($row));
                        if (!array_key_exists($e[0], $path)) {
                            $path[$e[0]] = [];
                        }
                        array_push($path[$e[0]], $e);
                    }
                }
                foreach ($path as $p) {
                    array_push($paths, $p);
                }
            }
        }
    }

    echo 'Day: ';
    $i = 0;
    $iend = 41;
    $istep = 4;
    while ($i < $iend) {
        if ($i > 0) {
            echo ' | ';
        }
        echo  '<a href="nam240p.php?bg=', $i, '&ed=', ($i + $istep - 1 < $iend) ? ($i + $istep - 1) : $iend, $pa == '' ? '' : '&pa=' . $pa, '">', ($i + $istep - 4) / 4, '</a>';
        $i += $istep;
    }
    echo '<br/>';

    if ($begin >= 0) {
        $i = $begin;
        while ($i <= $end) {
            $id = sprintf("%02d", $i);
            $it = 'NAM';
            $rg = 'NAM240';
            $stylepos = 'top: 0px; left: 0px; width:' . $scale[$it][$rg]['w'] . 'px; height: ' . $scale[$it][$rg]['h'] . 'px;';
            echo '<br/>';
            echo '<div style="position:relative; ', $stylepos, '">';
            $ran = rand(1, 1000000);
            echo '<img src="https://weatherstreet.com/gfs_files/gfs_clouds_us_', $i, '.png?=', $ran, '" alt="', $i, '">';
            echo '<svg style="position:absolute; ', $stylepos, '" onload="init(evt)">';
            // plot path
            foreach ($paths as $path) {
                plotpath($path, $it, $rg, 'path1');
                plotpath($path, $it, $rg, 'path2');
            }
            // plot marker
            foreach ($marker as $m) {
                plotmarker($m[1], $m[2], $it, $rg, 'line1');
                plotmarkerlabel($m[1], $m[2], $it, $rg, $m[0], $rg . '_' . $i);
            }
            echo '</svg>';
            echo '<span class="tooltip" id="', $rg . '_' . $i, '" style="position:absolute; visibility:hidden"> </span>';
            echo '</div>';
            $i++;
        }
    }



    include('tail.php');
    ?>
</body>

</html>