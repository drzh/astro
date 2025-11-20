<!DOCTYPE html>
<html>
<?php
include("../head.php") ?>
<body>
    <?php
    include('../menu.php');
    include('../libtable.php');

    if (isset($_GET['tb'])) {
        $tb = $_GET['tb'];
        # Sanitize the input to avoid directory traversal attacks
        $tb = basename($tb);
        display_table_from_tsv($tb);
        $lastUpdated = get_last_updated_time($tb);
        if ($lastUpdated) {
            echo "<p>Last updated: " . $lastUpdated . "</p>";
        }
    }

    include('../tail.php');
    ?>
</body>

</html>