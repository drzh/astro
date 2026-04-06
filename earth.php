<!DOCTYPE html>
<html>
<?php include('head.php') ?>
<body>
<?php
require 'menu.php';

echo "<div class='media-grid-two'>";
echo "<figure class='media-panel'><span class='media-panel__label'>Earth Rise</span><img src='http://api.usno.navy.mil/imagery/earth.png?view=rise&ID=AA-URL' alt='Earth rise view'></figure>";
echo "<figure class='media-panel'><span class='media-panel__label'>Earth Set</span><img src='http://api.usno.navy.mil/imagery/earth.png?view=set&ID=AA-URL' alt='Earth set view'></figure>";
echo "</div>";

include('tail.php');
?>
</body>
</html>
