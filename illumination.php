<html>
<?php include("head.php") ?>
<body>
<?php
  require 'menu.php';

  $e = explode(":", gmdate("Y:n:d:G:i"));

  echo "<img src='http://api.usno.navy.mil/imagery/earth.png?view=full&date=$e[1]/$e[2]/$e[0]&time=$e[3]:$e[4]&ID=AA-URL'>"
?>
</body>
</html>
