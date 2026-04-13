<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
<?php include 'menu.php'; ?>
<?php
$json_path = __DIR__ . '/table/atmospheric_optics.json';
$json_href = '/table/atmospheric_optics.json';
$payload = null;
$error_message = '';

$phenomena = array(
    'halo' => 'Halo',
    'parhelia' => 'Parhelia',
    'cza' => 'Circumzenithal Arc',
    'rainbow' => 'Rainbow',
);

if (is_file($json_path)) {
    $json = file_get_contents($json_path);
    if ($json === false) {
        $error_message = 'The atmospheric optics JSON file could not be read.';
    } else {
        $payload = json_decode($json, true);
        if (!is_array($payload)) {
            $payload = null;
            $error_message = 'The atmospheric optics JSON file is not valid JSON.';
        }
    }
} else {
    $error_message = 'The atmospheric optics JSON file does not exist yet.';
}

$format_percent = static function ($value) {
    if (!is_numeric($value)) {
        return '0.0%';
    }
    return number_format((float) $value * 100, 1) . '%';
};

$format_decimal = static function ($value) {
    if (!is_numeric($value)) {
        return '0.000';
    }
    return number_format((float) $value, 3);
};

$format_timestamp = static function ($value) {
    if (!is_string($value) || trim($value) === '') {
        return '';
    }

    try {
        $time = new DateTimeImmutable($value);
        return $time->format('Y-m-d H:i:s T');
    } catch (Exception $e) {
        return trim($value);
    }
};

$threshold = null;
$mode = '';
$latitude = null;
$longitude = null;
$generated_at = '';
$target_name = '';
$sources = array();

if (is_array($payload)) {
    if (isset($payload['threshold']) && is_numeric($payload['threshold'])) {
        $threshold = (float) $payload['threshold'];
    }
    if (isset($payload['mode']) && is_string($payload['mode'])) {
        $mode = $payload['mode'];
    }
    if (isset($payload['lat']) && is_numeric($payload['lat'])) {
        $latitude = (float) $payload['lat'];
    }
    if (isset($payload['lon']) && is_numeric($payload['lon'])) {
        $longitude = (float) $payload['lon'];
    }
    if (isset($payload['generated_at']) && is_string($payload['generated_at'])) {
        $generated_at = $payload['generated_at'];
    } elseif (is_file($json_path)) {
        $generated_at = gmdate('Y-m-d\TH:i:s\Z', filemtime($json_path));
    }
    if (isset($payload['target_name']) && is_string($payload['target_name'])) {
        $target_name = $payload['target_name'];
    }
    if (isset($payload['sources']) && is_array($payload['sources'])) {
        $sources = $payload['sources'];
    }
}

$maps_href = '';
if ($latitude !== null && $longitude !== null) {
    $maps_href = 'https://maps.google.com/maps?q=' . $latitude . ',' . $longitude;
}
?>
<section class="panel">
  <h1 class="panel-title">Atmospheric Optics</h1>
  <?php if ($payload === null): ?>
  <p><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php else: ?>
  <p class="weather-card__meta" style="margin:0 0 2px 0;">
    <?php
    $details = array();
    if ($mode !== '') {
        $details[] = 'Mode: ' . htmlspecialchars(ucfirst($mode), ENT_QUOTES, 'UTF-8');
    }
    if ($generated_at !== '') {
        $details[] = 'Updated: ' . htmlspecialchars($format_timestamp($generated_at), ENT_QUOTES, 'UTF-8');
    }
    echo implode(' | ', $details);
    ?>
  </p>
  <p class="weather-card__meta" style="margin:0 0 5px 0;">
    <?php if ($maps_href !== ''): ?>
    <a href="<?php echo htmlspecialchars($maps_href, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars(number_format((float) $latitude, 2) . ', ' . number_format((float) $longitude, 2), ENT_QUOTES, 'UTF-8'); ?></a>
    <span class="weather-card__dot" aria-hidden="true">&bull;</span>
    <?php endif; ?>
    <a href="<?php echo htmlspecialchars($json_href, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">Raw JSON</a>
  </p>
  <table class="table1">
    <thead>
      <tr>
        <th>Value</th>
        <th>Number</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($phenomena as $key => $label): ?>
      <?php
      $value = 0.0;
      if (isset($payload[$key]) && is_numeric($payload[$key])) {
          $value = (float) $payload[$key];
      }
      ?>
      <tr>
        <td><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($format_decimal($value), ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</section>
<?php if ($payload !== null): ?>
<section class="panel">
  <h2 class="panel-title">Data Sources</h2>
  <?php if ($sources === array()): ?>
  <p>No source timestamps were included in the current payload.</p>
  <?php else: ?>
  <ul>
    <?php foreach ($sources as $source): ?>
    <?php
    $name = '';
    $timestamp = '';
    if (is_array($source)) {
        if (isset($source['name']) && is_string($source['name'])) {
            $name = $source['name'];
        }
        if (isset($source['timestamp']) && is_string($source['timestamp'])) {
            $timestamp = $source['timestamp'];
        }
    }
    if (trim($name . ' ' . $timestamp) === '') {
        continue;
    }
    ?>
    <li><?php echo htmlspecialchars(trim($name . ' ' . $timestamp), ENT_QUOTES, 'UTF-8'); ?></li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</section>
<?php endif; ?>
<?php include 'tail.php'; ?>
</body>
</html>
