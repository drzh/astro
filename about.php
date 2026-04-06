<!DOCTYPE html>
<html>
<?php include('head.php') ?>
<body>
<?php include('menu.php'); ?>
<section class="panel">
  <p id="contact-link" class="weather-card__meta"></p>
</section>
<script>
  var user = 'admin';
  var host = 'goforreal.xyz';
  var comb = user + '@' + host;
  document.getElementById('contact-link').innerHTML = "Contact me: <a href='mailto:" + comb + "'>" + comb + "</a>";
</script>
<?php include('tail.php'); ?>
</body>
</html>
