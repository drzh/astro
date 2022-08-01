<?php
function calcx($x, $param) {
  return intval($param['marginleft'] + $param['mainwidth'] / ($param['xmax'] - $param['xmin']) * ($x - $param['xmin']));
}

function calcy($y, $param) {
  return intval($param['margintop'] + $param['mainheight'] - $param['mainheight'] / ($param['ymax'] - $param['ymin']) * ($y - $param['ymin']));
}

function plotline($xstart, $ystart, $xend, $yend, $type = 'line1', $param) {
  echo '<line x1="', $xstart, '" y1="', $ystart, '" x2="', $xend, '" y2="', $yend, '" class="', $type, '" />';
}  

function plottext($text, $x, $y, $type = 'sctext1', $param, $rotate=0) {
  $transform = ($rotate == 0) ? '' : 'transform=rotate(' . $rotate . ',' . ($x - 5) . ','. $y . ')';
  echo '<text x="', $x, '" y="', $y, '" class="', $type, '" ', $transform, '>', $text, '</text>';

}  

function plotcircle($x, $y, $r, $type = 'sccir1', $param) {
  echo '<circle cx="' . calcx($x, $param) . '" cy="' . calcy($y, $param) . '" r="' . $r . '" class="' . $type . '" />';
}

function plotrect($xstart, $ystart, $xend, $yend, $type = 'screct1', $param) {
  echo '<rect x="', calcx($xstart, $param), '" y="', calcy($yend, $param), '" width="', calcx($xend, $param) - calcx($xstart, $param), '" height="', calcy($ystart, $param) - calcy($yend, $param), '" class="', $type, '" />';
}  

function plotsvg($x, $y, $xlab='', $param, $type='') {
  if ($xlab == '') {
    $xlab = $x;
  }
  $panelwidth = $param['marginleft'] + $param['marginright'] + $param['mainwidth'];
  $panelheight = $param['margintop'] + $param['marginbottom'] + $param['mainheight'];
  $len = count($x);
  $param['xmin'] = min($x);
  $param['xmax'] = max($x);
  if ($param['xbreaks'] == '') {
    $step = ceil(($param['xmax'] - $param['xmin']) / 50);
    $param['xbreaks'] = range($param['xmax'], $param['xmin'], -$step);
  }
  if ($param['ybreaks'] == '') {
    $param['ybreaks'] = range($param['ymin'], $param['ymax'], ($param['ymax'] - $param['ymin']) / 5);
  }
  echo '<div style="position:relative; width:', $panelwidth, 'px; height:', $panelheight, 'px;">';
  echo '<svg style="position:absolute; top:0px; left:0px; width:', $panelwidth, 'px; height: ', $panelheight, 'px;">';
  // Plot subgrids
  foreach ($param['xbreaks'] as $xb) {
    plotline(calcx($xb, $param), calcy($param['ymin'], $param), calcx($xb, $param), calcy($param['ymax'], $param), 'scline1', $param);
    for ($i = 0; $i < $len; $i++) {
      if ($x[$i] == $xb) {
        plottext($xlab[$i], calcx($xb, $param), calcy($param['ymin'], $param), 'sctext2', $param, 90);
        break;
      }
    }
  }
  foreach ($param['ybreaks'] as $yb) {
    plotline(calcx($param['xmin'], $param), calcy($yb, $param), calcx($param['xmax'], $param), calcy($yb, $param), 'scline1', $param);
    plottext($yb, calcx(0, $param) - 10, calcy($yb, $param) + 5, 'sctext1', $param);
    plottext($yb, calcx($param['xmax'], $param) + 10, calcy($yb, $param) + 5, 'sctext2', $param);
  }
  // Plot data
  for ($i = 0; $i < $len; $i++) {
    $t = ($type == '') ? 'sccir1' : $type[$i];
    plotcircle($x[$i], $y[$i], 2, $t, $param);
  }
  echo '</svg>';
  echo '</div>';
}

?>
