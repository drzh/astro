<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';
require 'planets_menu.php';

$filepattern = array(array('planet/all/*.multi_info.*.format',
                           'Planet Rise and Set'),
);

$rowcolor = array('#444444', '#333333');
$today = date('Ymd');

foreach ($filepattern as $fp) {
  echo '<table width=800 border=1 style="float:left; margin-right:30px;">';
  echo '<caption><b>Planets</b></caption>';
  $i = 0;
  foreach (glob($fp[0]) as $f) {
    preg_match('/([A-Z]{3}).format/', $f, $matches, PREG_OFFSET_CAPTURE);
    $tz = $matches[1][0];
    $daypre = '';
    $fh = fopen($f, "r") or die("Cannot open file!\n");
    while(! feof($fh)) {
      $e = explode("\t", fgets($fh));
      if (count($e) == 10) {
        if ($i == 0) {
          foreach (range(1, 9) as $j) {
            $e[$j] = rtrim($e[$j]);
            echo '<th bgcolor="', $rowcolor[($j + 1) % 2], '">', $e[$j], '</th>';
          }
          $i = 1;
        }
        else {
          $day = date("Ymd", strtotime($e[0] . ' ' . $tz));
          $daydiff = (strtotime($day) - strtotime($today)) / 86400;
          if ($daydiff > 6) {
            break;
          }
          if ($daydiff >= 0) {
            if ($day != $daypre) {
              $daypre = $day;
              $day = date("D, n/j", strtotime($e[0] . ' ' . $tz));
              echo '<tr align="center" bgcolor="#222222"><td colspan="9"><b><font>', $day, '</font></b></td></tr>';
            }
            echo '<tr align="center">';
            foreach (range(1, 9) as $j) {
              $e[$j] = rtrim($e[$j]);
              echo '<td bgcolor="', $rowcolor[($j + 1) % 2], '">', $e[$j], '</td>';
            }
            echo '</tr>';
          }
        }
      }
    }
  }
}
echo '</table>';
?>
</body>
</html>
