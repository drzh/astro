<?php
# Read in a json file from URL parameter 'json' and display it as a table.

include("../head.php");
include('../menu.php');

if (isset($_GET['json'])) {
    $json = $_GET['json'];
    $json = basename($json);
    $fh = fopen($json, "r") or die("Cannot open file!\n");
    $json = fread($fh, filesize($json));
    $data = json_decode($json, true);

    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

include('../tail.php');
?>