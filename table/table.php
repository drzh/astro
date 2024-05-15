<!DOCTYPE html>
<html>
<?php
include("../head.php") ?>

<body>
    <?php
    include('../menu.php');

    # Read the tsv file from the variable 'tb' in URL and display it as a table, and the color of the rows are alternating.
    if (isset($_GET['tb'])) {
        $tb = $_GET['tb'];
        # Remove the path from the file name to avoid security issue.
        $tb = basename($tb);
        $fh = fopen($tb, "r") or die("Cannot open file!\n");
        echo "<table class='table1'>";
        $i = 1;
        while (!feof($fh)) {
            $e = explode("\t", fgets($fh));
            # Check if the $e is only one element, then skip it.
            if (count($e) == 1 && $e[0] == "") {
                continue;
            }
            echo "<tr>";
            foreach ($e as $f) {
                echo "<td class='td" . $i % 2 . "'>" . $f . "</td>";
            }
            echo "</tr>";
            $i = $i + 1;
        }
        echo "</table>\n";
        $lastUpdated = filemtime($tb);
        echo "<p>Last updated: " . date("Y-m-d H:i:s", $lastUpdated) . "</p>";
    }

    include('../tail.php');
    ?>
</body>

</html>