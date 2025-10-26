<?php

$tz = 'America/Chicago';
$event = array('sun', 'civil', 'nautical', 'astronomical', 'moon', 'moon_phase');
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

#// Moon phase
#$moonphase = "<tr align='center'>";
#$moonphase .= "<td>moon phase</td>";
#$fname = 'data/moon.phase.2024_2035.CDT.format';
##$fh = fopen($fname, "r") or die("Cannot open file: $fname!\n");
#foreach ($days as $day) {
  #$fh = fopen($fname, "r") or die("Cannot open file: $fname!\n");
  #$phase = "-";
  #while(! feof($fh)) {
    #$e = explode("\t", fgets($fh));
    #$e[1] = rtrim($e[1]);
    #if ($e[0] == $day) {
      #$phase = $e[1];
      ##if ($phase != '-') {
        ##$phase *= 100;
      ##}
      #break;
    #}
    #if (strcmp($e[0], $day) > 0) {
      #break;
    #}
  #}
  #fclose($fh);
  #$moonphase .= "<td colspan='2' bgcolor='" . datecolor($today, $day) . "'>" . $phase . "</td>";
#}
#$moonphase .= "</tr>";
#fclose($fh);

foreach ($pos as $p) {
  echo "<h2><a href='$p[4]' target='_blank'>$p[0]</a> (<a href='http://maps.google.com/maps?q=$p[1],$p[2]' target='_blank'>$p[1], $p[2]</a>)</h2>";
  if (glob('data/' . $p[5] . '.*')) {
    echo "<table width=1000 border=1>";
    echo "<tr align='center'>", "\n";
    echo "<th>Day</th>", "\n";
    foreach ($days as $day) {
      echo "<th colspan='2' bgcolor='", datecolor($today, $day), "'>", date("D, n/j", strtotime($day)), "</th>", "\n";
    }
    echo "</tr>", "\n";
    echo "<th align='center'>Event</th>", "\n";
    foreach ($days as $day) {
      echo "<th align='center' bgcolor='", datecolor($today, $day), "'>Begin</th>";
      echo "<th align='center' bgcolor='", datecolor($today, $day), "'>End</th>", "\n";
    }
    echo "</tr>", "\n";
    foreach ($event as $eve) {
      $fname = 'data/' . $p[5] . "." . $eve . ".2024_2035.CDT.format";
      if (!file_exists($fname)) {
        echo "Cannot find file: $fname\n";
        continue;
      }
      echo "<tr align='center'>";
      echo "<td valign='top'>", $eve, "</td>";
      foreach ($days as $day) {
        $begin = "-";
        $end = "-";
        $fh = fopen($fname, "r") or die("Cannot open file!\n");
        while(! feof($fh)) {
          $e = explode("\t", fgets($fh));
          $e[2] = rtrim($e[2]);
	        if ($e[0] == $day) {
            if (strcmp($e[1], "-") != 0) {
              # Check If $e[1] is four digits integer
              if (strlen($e[1]) == 4 && ctype_digit($e[1])) {
                $begin = date("H:i", strtotime($e[0] . $e[1] . " " . $tz));
              } else {
                $begin = $e[1];
              }
            }
            if (strcmp($e[2], "-") != 0) {
              # Check If $e[2] is four digits integer
              if (strlen($e[2]) == 4 && ctype_digit($e[2])) {
                $end = date("H:i", strtotime($e[0] . $e[2] . " " . $tz));
              } else {
                $end = $e[2];
              }
            }
            break;
          }
	      }
	      fclose($fh);
        echo "<td valign='top' bgcolor='", datecolor($today, $day), "'>", $begin, "</td>";
        echo "<td valign='top' bgcolor='", datecolor($today, $day), "'>", $end, "</td>";
        //echo "<td valign='top' bgcolor='", datecolor($day), "'>", strtotime($day), ', ', strtotime($today), ', ', (strtotime($day) - strtotime($today)) / 86400, "</td>";
      }
      echo "</tr>", "\n";
    }
    echo $moonphase;
    echo "</table>", "\n";
  }
  echo "<hr>", "\n";
}
?>
