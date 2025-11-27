<?php
# Read the tsv file from the variable
function display_table_from_tsv($para_tb) {
    if (isset($para_tb)) {
        # Remove the path from the file name to avoid security issue.
        $fh = fopen($para_tb, "r") or die("Cannot open file!\n");
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
    }
}

function get_last_updated_time($para_tb) {
    if (isset($para_tb)) {
        $lastUpdated = filemtime($para_tb);
        return date("Y-m-d H:i:s", $lastUpdated);
    }
    return null;
}
?>