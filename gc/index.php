<!DOCTYPE html>
<html>
<body>
<center>
<a href='index.php'>New input</a><br/><br/>
<?php
if (isset($_GET['gcinput'])) {
  $gcinput = $_GET['gcinput'];

  // $fp = fopen("img/tmp.input", "w");
  // fwrite($fp, $gcinput);
  // fclose($fp);
  // $imgdata = shell_exec("cat img/tmp.input | ./generate_gc.sh ");

  $cmd = "echo '$gcinput' | ./generate_gc.sh ";
  $imgdata = shell_exec($cmd);

  $imgs = explode("\n", trim($imgdata));
  foreach ($imgs as $img) {
    if ($img != '') {
      echo '<img src="data:image/png;base64, ', $img, '">', '<br/><br/>';
    }
  }
}
else {
?>
<h1>INPUT: card number, pin, amount</h1>
<form action="index.php" method="get">
<textarea name="gcinput" rows="20" cols="50"></textarea><br/>
<input type="submit">
</form>
<?php
}
?>
</center>
</body>
</html> 
