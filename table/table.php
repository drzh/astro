<!DOCTYPE html>
<html>
<?php
include("../head.php") ?>
<body>
    <?php
    include('../menu.php');
    require_once __DIR__ . '/../includes/table.php';

    if (isset($_GET['tb'])) {
        $tb = $_GET['tb'];
        # Sanitize the input to avoid directory traversal attacks
        $tb = basename($tb);
        display_table_from_tsv($tb, 1);
    }

    include('../tail.php');
    ?>
</body>

</html>
