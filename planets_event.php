<?php
$rowcolor = array('#333333', '#444444');
$today = date('Ymd');

// function cmp($a, $b) {
//   if ($a[2] . $a[3] == $b[2] . $b[3]) return 0;
//   return ($a[2] . $a[3] < $b[2] . $b[3]) ? -1 : 1;
// }

function cmp($a, $b) {
  if ($a[4] == $b[4]) return 0;
  return ($a[4] < $b[4]) ? -1 : 1;
}

$nrec = 0;
foreach ($filepattern as $fp) {
  $rec = array();
  foreach (glob($fp[0]) as $f) {
    preg_match('/([A-Z]{3}).format/', $f, $matches, PREG_OFFSET_CAPTURE);
    $tz = $matches[1][0];
    $fh = fopen($f, "r") or die("Cannot open file!\n");
    while(! feof($fh)) {
      $e = explode("\t", fgets($fh));
      if (count($e) == 4) {
        array_push($e, strtotime($e[2] . $e[3] . " " . $tz));
        $e[3] = rtrim($e[3]);
        $day = date("Ymd", strtotime($e[2] . $e[3] . " " . $tz));
        $daydiff = (strtotime($day) - strtotime($today)) / 86400;
        if ($daydiff >= 0 && $daydiff <= 6) {
          array_push($rec, $e);
          $nrec++;
        }
	else if ($daydiff > 6) {
	  break;
	}
      }
    }
  }
  usort($rec, 'cmp');

  // Set visable status
  $visflag = array();
  $vis = array();
  $i = 0;
  while ($i < $nrec) {
    if (! array_key_exists($rec[$i][0], $vis)) {
      $visflag[$rec[$i][0]] = 0;
      $vis[$rec[$i][0]] = 1;
    }
    if ($rec[$i][1] == 'Rise') {
      if ($visflag[$rec[$i][0]] == 0) {
        $visflag[$rec[$i][0]] = 1;
        $vis[$rec[$i][0]] = 0;
      }
    }
    if ($rec[$i][1] == 'Set') {
      if ($visflag[$rec[$i][0]] == 0) {
        $visflag[$rec[$i][0]] = 1;
      }
    }
    $i++;
  }
  $i = 0;  
  while ($i < $nrec) {
    if ($rec[$i][1] == 'Rise') {
      $vis[$rec[$i][0]] = 1;
    }
    $rec[$i][5] = $vis[$rec[$i][0]];
    if ($rec[$i][1] == 'Set') {
      $vis[$rec[$i][0]] = 0;
    }
    $i++;
  }

  // Print table
  echo "<table width=300 border=1 style='float:left; margin-right:30px;'>";
  echo "<caption><b>", $fp[1], "</b></caption>";
  echo "<tr align='center'>";
  echo "<th>Planet</th>";
  echo "<th>Event</th>";
  echo "<th>Time</th>";
  echo "</tr>", "\n";
  $i = 0;
  $daypre = '';
  foreach ($rec as $e) {
    if ($e[5] == 1) {
      $i = ++$i % 2;
      // $day = date("D, n/j", strtotime($e[2] . $e[3] . " " . $e[4]));
      $day = date("D, n/j", $e[4]);
      if ($day != $daypre) {
        echo '<tr align="center" bgcolor="#666666"><td colspan="3"><b><font color="lightgray">', $day, '</font></b></td></tr>';
        $i = 0;
      }
      $daypre = $day;
      // $time = date("H:i", strtotime($e[2] . $e[3] . " " . $e[4]));
      $time = date("H:i", $e[4]);
      echo '<tr align="center" bgcolor="', $rowcolor[$i], '"><td>', $e[0], '</td><td>';
      // Format rise and set with arrow
      if ($e[1] == 'Rise') {
        echo '&uarr;&nbsp;', $e[1], '&nbsp;&uarr;';
      }
      else if ($e[1] == 'Set') {
        echo '&darr;&nbsp;', $e[1], '&nbsp;&darr;';
      }
      else {
        echo $e[1];
      }
      echo '</td><td>', $time, '</td></tr>';
    }
  }
  echo '</table>', "\n";
}

?>
