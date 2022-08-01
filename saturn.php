<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';
require 'planets_menu.php';

$filepattern = array(array('planet/saturn/saturn.*.format',
                           'Saturn Satellites'),
);

$today = date('Ymd');

echo '<b>Saturn Satellites</b>', "\n";
echo "<pre>";
echo "                T: Titan  R: Rhea  d: Dione  t: Tethys  j: Iapetus  e: Enceladus  m: Mimas", "\n";

foreach ($filepattern as $fp) {
  foreach (glob($fp[0]) as $f) {
    preg_match('/([A-Z]{3}).format/', $f, $matches, PREG_OFFSET_CAPTURE);
    $tz = $matches[1][0];
    $daypre = '';
    $fh = fopen($f, "r") or die("Cannot open file!\n");
    while(! feof($fh)) {
      $e = explode("\t", fgets($fh));
      if (count($e) == 3) {
        $e[2] = rtrim($e[2]);
        $day = date("Ymd", strtotime($e[0] . ' ' . $tz));
        $daydiff = (strtotime($day) - strtotime($today)) / 86400;
        if ($daydiff >= 0 && $daydiff <= 6) {
          if ($day != $daypre) {
            if ($daypre != '') {
              echo '       -------------------------------------------------------------------------------------', "\n";
              echo 'Scale: 1 0 9 8 7 6 5 4 3 2 1 0 9 8 7 6 5 4 3 2 1 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1', "\n";
              echo '       -------------------------------------------------------------------------------------', "\n";
            }
            $daypre = $day;
            $day = date("D, n/j", strtotime($e[0] . $tz));
            echo '<font style="color: #FFFFFF; background-color: #666666">', $day, "</font>\n";
          }
          $time = date("H:i", strtotime($e[1] . $tz));
          echo $time, '  ', $e[2], "\n";
        }
      }
    }
  }
}

echo "</pre>\n";
?>
</body>
</html>
