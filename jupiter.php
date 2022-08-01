<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';
require 'planets_menu.php';

$filepattern = array(array('planet/jupiter/jupiter.*.format',
                           'Jupiter Events'),
);

require 'planets_event.php';
?>
</body>
</html>
