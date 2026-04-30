<?php
# Read in a json file from URL parameter 'json' and display it as a table.

$page_title = 'JSON Display';
?>
<!DOCTYPE html>
<html>
<?php
include __DIR__ . '/../../head.php';
?>
<body>
<?php
include __DIR__ . '/../../menu.php';
if (isset($_GET['json'])) {
    $json = $_GET['json'];
    $json = basename($json);
    $json_path = __DIR__ . '/../data/' . $json;
    $fh = fopen($json_path, "r") or die("Cannot open file!\n");
    $json = fread($fh, filesize($json_path));
    $data = json_decode($json, true);

    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

include __DIR__ . '/../../tail.php';
?>
</body>
</html>
