<?php

$event = array('sun', 'civil', 'nautical', 'astronomical', 'moon');
$today = date('Ymd');
$days = array(
  $today,
  date('Ymd', strtotime('+1 day', strtotime($today))),
  date('Ymd', strtotime('+2 day', strtotime($today))),
  date('Ymd', strtotime('+3 day', strtotime($today))),
  date('Ymd', strtotime('+4 day', strtotime($today))),
  date('Ymd', strtotime('+5 day', strtotime($today))),
  date('Ymd', strtotime('+6 day', strtotime($today))),
);

function datecolor($day1, $day2) {
  # if (($day2 - $day1) % 2 == 0) {
  if ((strtotime($day2) - strtotime($day1)) / 86400 % 2 == 0) {
    return "#444444";
  }
  else {
    return "#333333";
  }
}

// Moon phase
$moonphase = "<tr align='center'>";
$moonphase .= "<td>moon phase</td>";
$fname = 'data/moon.phase.2016_2025.CST.format';
$fh = fopen($fname, "r") or die("Cannot open file: $fname!\n");
foreach ($days as $day) {
  $phase = "-";
  while(! feof($fh)) {
    $e = explode("\t", fgets($fh));
    $e[1] = rtrim($e[1]);
    if ($e[0] == $day) {
      $phase = $e[1];
      if ($phase != '-') {
        $phase *= 100;
      }
      break;
    }
  }
  $moonphase .= "<td colspan='2' bgcolor='" . datecolor($today, $day) . "'>" . $phase . "</td>";
}
$moonphase .= "</tr>";
fclose($fh);

foreach ($pos as $p) {
  echo "<h2><a href='$p[4]' target='_blank'>$p[0]</a> (<a href='http://maps.google.com/maps?q=$p[1],$p[2]' target='_blank'>$p[1], $p[2]</a>)</h2>";
  if (glob('data/' . $p[5] . '.*')) {
    echo "<table width=1000 border=1>";
    echo "<tr align='center'>", "\n";
    echo "<th>Day</th>", "\n";
    foreach ($days as $day) {
      echo "<th colspan='2' bgcolor='", datecolor($today, $day), "'>", date("D, n/j", strtotime($day . " CST")), "</th>", "\n";
    }
    echo "</tr>", "\n";
    echo "<th align='center'>Event</th>", "\n";
    foreach ($days as $day) {
      echo "<th align='center' bgcolor='", datecolor($today, $day), "'>Begin</th>";
      echo "<th align='center' bgcolor='", datecolor($today, $day), "'>End</th>", "\n";
    }
    echo "</tr>", "\n";
    foreach ($event as $eve) {
      $fname = 'data/' . $p[5] . "." . $eve . ".2016_2025.CST.format";
      $fh = fopen($fname, "r") or die("Cannot open file!\n");
      echo "<tr align='center'>";
      echo "<td valign='top'>", $eve, "</td>";
      foreach ($days as $day) {
        $begin = "-";
        $end = "-";
        while(! feof($fh)) {
          $e = explode("\t", fgets($fh));
          $e[2] = rtrim($e[2]);
          if ($e[0] == $day) {
            if (strcmp($e[1], "-") != 0) {
              $begin = date("H:i", strtotime($e[0] . $e[1] . " CST"));
            }
            if (strcmp($e[2], "-") != 0) {
              $end = date("H:i", strtotime($e[0] . $e[2] . " CST"));
            }
            break;
          }
        }
        echo "<td valign='top' bgcolor='", datecolor($today, $day), "'>", $begin, "</td>";
        echo "<td valign='top' bgcolor='", datecolor($today, $day), "'>", $end, "</td>";
        //echo "<td valign='top' bgcolor='", datecolor($day), "'>", strtotime($day), ', ', strtotime($today), ', ', (strtotime($day) - strtotime($today)) / 86400, "</td>";
      }
      echo "</tr>", "\n";
      fclose($fh);
    }
    echo $moonphase;
    echo "</table>", "\n";
  }
  echo "<hr>", "\n";
}
?>
