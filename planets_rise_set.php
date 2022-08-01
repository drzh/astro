<html>
<?php include("head.php") ?>
<body>
<?php
require 'menu.php';
require 'planets_menu.php';

$filepattern = array(array('planet/rise_set/*.rise_set.*.format',
                           'Planet Rise and Set'),
);

require 'planets_event.php';
?>
</body>
</html>
