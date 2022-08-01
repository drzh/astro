<!DOCTYPE html>
<html>
<?php
include("head.php") ?>
<body>
<?php
include('menu.php');
#echo "<p><img src='data/ct.jpg'></p>";
?>
<script language='javascript'>
  var user = 'admin';
  var host = 'goforreal.xyz';
  var comb = user + '@' + host;
  document.write("Contact me: <a href='" + "mail" + "to:" + comb + "'>" + comb + "</a>");
</script>
<?php
include('tail.php');
?>
</body>
</html>
