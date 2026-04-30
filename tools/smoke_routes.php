<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/layout.php';

$root = dirname(__DIR__);
$routes = array();

foreach (astro_menu_rows() as $row) {
    foreach ($row as $item) {
        $href = (string) ($item['href'] ?? '');
        if ($href === '' || strpos($href, 'http') === 0) {
            continue;
        }
        $routes[$href] = $href;
    }
}

$routes['/table.php?tbm=satellite_ham&sat=ALL_PRI&max=20'] = '/table.php?tbm=satellite_ham&sat=ALL_PRI&max=20';
$routes['/table.php?tbm=satellite_vis&sat=All&mag=3&max=20'] = '/table.php?tbm=satellite_vis&sat=All&mag=3&max=20';

$failures = array();
foreach (array_values($routes) as $route) {
    $result = astro_smoke_route($root, $route);
    $status = $result['ok'] ? 'ok' : 'FAIL';
    echo str_pad($status, 5), ' ', $route, ' (', $result['bytes'], " bytes)\n";

    if (!$result['ok']) {
        $failures[] = $route . ': ' . $result['message'];
    }
}

if ($failures !== array()) {
    fwrite(STDERR, "\nFailures:\n- " . implode("\n- ", $failures) . "\n");
    exit(1);
}

exit(0);

function astro_smoke_route($root, $route)
{
    $parts = parse_url($route);
    $path = $parts['path'] ?? '';
    $query = $parts['query'] ?? '';
    if ($path === '') {
        return array('ok' => false, 'bytes' => 0, 'message' => 'empty route path');
    }

    $relative_path = ltrim($path, '/');
    $file = realpath($root . '/' . $relative_path);
    if ($file === false || !is_file($file)) {
        return array('ok' => false, 'bytes' => 0, 'message' => 'route file missing');
    }

    $root_real = realpath($root);
    if ($root_real === false || strpos($file, $root_real . DIRECTORY_SEPARATOR) !== 0) {
        return array('ok' => false, 'bytes' => 0, 'message' => 'route file outside astro root');
    }

    $code = <<<'PHP'
parse_str($argv[1], $_GET);
$_POST = array();
$_REQUEST = $_GET;
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = $argv[2];
$_SERVER['SCRIPT_NAME'] = $argv[3];
$_SERVER['PHP_SELF'] = $argv[3];
chdir($argv[4]);
ob_start();
include $argv[5];
$html = ob_get_clean();
if (preg_match('/(?:Fatal error|Parse error|Warning|Notice|Deprecated):/i', $html, $matches)) {
    fwrite(STDERR, $matches[0] . "\n");
    exit(1);
}
if (trim($html) === '') {
    fwrite(STDERR, "empty response\n");
    exit(2);
}
echo strlen($html), "\n";
PHP;

    $command = array(
        PHP_BINARY,
        '-d',
        'display_errors=1',
        '-r',
        $code,
        $query,
        $route,
        $path,
        dirname($file),
        $file,
    );
    $descriptors = array(
        0 => array('pipe', 'r'),
        1 => array('pipe', 'w'),
        2 => array('pipe', 'w'),
    );
    $process = proc_open($command, $descriptors, $pipes, $root);
    if (!is_resource($process)) {
        return array('ok' => false, 'bytes' => 0, 'message' => 'could not start PHP subprocess');
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exit_code = proc_close($process);

    $bytes = (int) trim($stdout);
    if ($exit_code !== 0) {
        $message = trim($stderr) !== '' ? trim($stderr) : 'exit ' . $exit_code;
        return array('ok' => false, 'bytes' => $bytes, 'message' => $message);
    }

    return array('ok' => true, 'bytes' => $bytes, 'message' => '');
}
