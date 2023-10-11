<?php
  $today = date('Ymd');
  $nhourmax = 168;
  // $nhourmax = 150;
  $marginleft = 35;
  $margintop = 20;
  $marginright = 30;
  $marginbottom = 25;
  #$mainwidth = 800;
  $mainwidth = $nhourmax * 10;
  $mainheight = 100;

  $ybreaksmain = array(0, 20, 40, 60, 80, 100);
  $ybreakssub = array();
  $ybreaksright = array(-10, 0, 10, 20, 30, 40);

  $whitebegin = 6;
  $whiteend = 18;

  $panelwidth = $marginleft + $marginright + $mainwidth;
  $panelheight = $margintop + $marginbottom + $mainheight;

  function calcx($nhour)
  {
    global $marginleft, $margintop, $marginright, $marginbottom, $mainwidth, $mainheight, $nhourmax;
    return intval($marginleft + $mainwidth / $nhourmax * $nhour);
  }

  function calcy($cover)
  {
    global $marginleft, $margintop, $marginright, $marginbottom, $mainwidth, $mainheight, $nhourmax;
    return intval($margintop + $mainheight - $mainheight / 100 * $cover);
  }

  function plotline($xstart, $ystart, $xend, $yend, $type = 'line1')
  {
    echo '<line x1="', calcx($xstart), '" y1="', calcy($ystart), '" x2="', calcx($xend), '" y2="', calcy($yend), '" class="', $type, '" />';
  }

  function plottext($text, $x, $y, $type = 'sctext1')
  {
    echo '<text x="', calcx($x), '" y="', calcy($y), '" class="', $type, '">', $text, '</text>';
  }

  function plotcircle($x, $y, $r, $type = 'sccir1')
  {
    // echo '<circle cx="', calcx($x), '" cy="', calcy($y), '" r="', $r, '" class="', $type, '" />';
    return '<circle cx="' . calcx($x) . '" cy="' . calcy($y) . '" r="' . $r . '" class="' . $type . '" />';
  }

  function plotrect($xstart, $ystart, $xend, $yend, $type = 'screct1')
  {
    echo '<rect x="', calcx($xstart), '" y="', calcy($yend), '" width="', calcx($xend) - calcx($xstart), '" height="', calcy($ystart) - calcy($yend), '" class="', $type, '" />';
  }

  // Legend
  echo '<svg style="width:300px; height:20px;">';
  echo '<circle cx=5 cy=10 r=3 class="sccir1" />';
  echo '<text x=15 y=15 class="sctext2">SkyCover</text>';
  echo '<circle cx=105 cy=10 r=2 class="sccir2" />';
  echo '<text x=115 y=15 class="sctext2">Humidity</text>';
  echo '<circle cx=205 cy=10 r=2 class="sccir3" />';
  echo '<text x=215 y=15 class="sctext2">Temp</text>';
  echo '</svg>';

  foreach ($pos as $p) {
    echo "<h2><a href='$p[4]' target='_blank'>$p[0]</a> (<a href='http://maps.google.com/maps?q=$p[1],$p[2]' target='_blank'>$p[1], $p[2]</a>)</h2>";
    echo '<div style="position:relative; width:', $panelwidth, 'px; height:', $panelheight, 'px;">';
    $lenbar = 5;
    echo '<svg style="position:absolute; top:0px; left:0px; width:', $panelwidth, 'px; height: ', $panelheight, 'px;">';
    // Plot data
    #$fname = $datadir . '/all.skycover.3day.UTC.format';
    $fname = $f1;
    preg_match('/([A-Z]{3}).format/', $fname, $matches, PREG_OFFSET_CAPTURE);
    $tz = $matches[1][0];
    $i = 0;
    $daypre = '';
    $timepre = 0;
    $time0 = 0;
    $nhourpre = 0;
    // $fh = fopen($fname, "r") or die("Cannot open file: $fname!\n");
    $fh = fopen($fname, "r");
    if (!$fh) {
      echo "<p>Cannot open file: $fname!</p>";
      continue;
    }
    $plotrec1 = '';
    while (!feof($fh)) {
      $e = explode("\t", trim(fgets($fh)));
      if (count($e) == 3) {
        if ($e[0] == $p[0]) {
          $time = strtotime($e[1] . ' ' . $tz);
          $day = date('Ymd', strtotime($e[1] . ' ' . $tz));
          $hour = date('H', strtotime($e[1] . ' ' . $tz));
          if ($time0 == 0) {
            $time0 = $time;
            // Plot day and night block
            $day0 = $day;
            $timebegin = strtotime('-6 hour', strtotime($day0));
            $timeend = strtotime('+6 hour', strtotime($day0));
            if ($time0 < $timeend) {
              plotrect(0, 0, ($timeend - $time0) / 3600, 0, 'screct1');
            }
            $timemax = strtotime('+' . $nhourmax . ' hour', $time0);
            while ($timebegin < $timemax) {
              plotrect(($timebegin > $time0) ? ($timebegin - $time0) / 3600 : 0, 0, ((($timeend < $timemax) ? $timeend : $timemax) - $time0) / 3600, 100, 'screct1');
              $timebegin = strtotime('+1 day', $timebegin);
              $timeend = strtotime('+1 day', $timeend);
            }
            // Plot subgrids
            foreach ($ybreaksmain as $y) {
              plotline(0, $y, $nhourmax, $y, 'scline1');
              plottext($y, -0.5, $y - 5, 'sctext1');
            }
            // Plot right labels
            foreach ($ybreaksright as $y) {
              plottext($y, $nhourmax + 0.5, $y / 50 * 100 + 15, 'sctext4');
            }
            // Plot day boundary
            $timedayb = strtotime($day0);
            #$timedayend = strtotime('+3 day', $time0);
            $timedayend = strtotime('+' . ceil($nhourmax / 24) . ' day', $time0);
            while ($timedayb <= $timedayend) {
              if ($timedayb >= $time0) {
                # if ($nhour <= $nhourmax) {
                $nhour = ($timedayb - $time0) / 3600;
                # }
                plotline($nhour, 0, $nhour, 120, 'scline1');
                if ($nhour > 6 && $nhour <= $nhourmax) {
                  plottext(date('D, n/j', $timedayb - 86400), $nhour - 1, 105, 'sctext1');
                }
                if ($nhour < $nhourmax - 10) {
                  plottext(date('D, n/j', $timedayb), $nhour + 1, 105, 'sctext2');
                }
              }
              $timedayb = strtotime('+1 day', $timedayb);
            }
          }
          if ($time > $timemax) {
            break;
          }
          $nhour = ($time - $time0) / 3600;
          if ($hour % 3 == 0 || $nhour - $nhourpre >= 2) {
            plotline($nhour, 0, $nhour, -6, 'scline3');
            plottext($hour, $nhour, -20, 'sctext3');
          }
          $plotrec1 .= plotcircle($nhour, $e[2], 3, 'sccir1');
          $timepre = $time;
          $nhourpre = $nhour;
        }
      }
    }

    # Plot relative humidity
    #$fname = $datadir . '/all.rhm.3day.UTC.format';
    $fname = $f2;
    if (file_exists($fname)) {
      preg_match('/([A-Z]{3}).format/', $fname, $matches, PREG_OFFSET_CAPTURE);
      $tz = $matches[1][0];
      $fh = fopen($fname, "r");
      if ($fh) {
        $plotrec2 = '';
        while (!feof($fh)) {
          $e = explode("\t", trim(fgets($fh)));
          if (count($e) == 3) {
            if ($e[0] == $p[0]) {
              $time = strtotime($e[1] . ' ' . $tz);
              if ($time > $timemax) {
                break;
              }
              $nhour = ($time - $time0) / 3600;
              $plotrec2 .= plotcircle($nhour, $e[2], 1, 'sccir2');
            }
          }
        }
      }
    }

    # Plot temprature
    #$fname = $datadir . '/all.temp.3day.UTC.format';
    $fname = $f3;
    # Check if the file exists
    if (file_exists($fname)) {
      $temphigh = 40;
      $templow = -10;
      preg_match('/([A-Z]{3}).format/', $fname, $matches, PREG_OFFSET_CAPTURE);
      $tz = $matches[1][0];
      $fh = fopen($fname, "r");
      if ($fh) {
        $plotrec3 = '';
        while (!feof($fh)) {
          $e = explode("\t", trim(fgets($fh)));
          if (count($e) == 3) {
            if ($e[0] == $p[0]) {
              $time = strtotime($e[1] . ' ' . $tz);
              if ($time > $timemax) {
                break;
              }
              $nhour = ($time - $time0) / 3600;
              if (is_numeric($e[2])) {
                $plotrec3 .= plotcircle($nhour, ($e[2] - 273 - $templow) / ($temphigh - $templow) * 100, 1, 'sccir3');
              } else {
                $plotrec3 .= plotcircle($nhour, 0, 1, 'sccir3');
              }
            }
          }
        }
      }
    }

    echo $plotrec3, $plotrec2, $plotrec1;
    echo '</svg>';
    echo '</div>';
    // echo '<br />';
  }

?>