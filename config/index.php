<html>
<?php include("../head.php") ?>
<body>
<?php
require '../menu.php';

$toggle = [
  'GOES' => 'tggoes',
  'SkyCover' => 'tgsky',
  'SkyCoverUS' => 'tgskyus',
  'SatVis_Alert' => 'tgsatvisalt',
  'SatHam_Alert' => 'tgsathamalt',
];

echo '<table width=200 border=1>';

foreach ($toggle as $k => $v) {
  $tgf = '' . $v . '.off';
  # Change status
  if (isset($_GET[$v])) {
    $st = $_GET[$v];
    if ($st == 'on' && file_exists($tgf)) {
      unlink($tgf);
    }
    if ($st == 'off' && (! file_exists($tgf))) {
      fopen($tgf, 'w') or die("Unable to open file: $tgf !");
    }
  }

  # Status table
  echo '<tr align="center">', "\n";
  echo '<td>', $k, '</td>';
  echo '<td>';
  if (file_exists($tgf)) {
    echo '<a class="statoff" href="index.php?', $v, '=on">Off</a>';
  }
  else {
    echo '<a class="staton" href="index.php?', $v, '=off">On</a>';
  }
  echo '</td>';
  echo '</tr>';
}

echo '</table>';

include('../tail.php');
?>
</body>
</html>
