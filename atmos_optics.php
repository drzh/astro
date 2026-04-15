<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
<?php include 'menu.php'; ?>
<?php
$json_path = __DIR__ . '/table/atmospheric_optics.json';
$json_href = '/table/atmospheric_optics.json';
$payload = null;
$prediction = array();
$request = array();
$target = array();
$sources = array();
$phenomena = array();
$error_message = '';

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

if (is_array($payload)) {
    if (isset($payload['prediction']) && is_array($payload['prediction'])) {
        $prediction = $payload['prediction'];
    }
    if (isset($prediction['request']) && is_array($prediction['request'])) {
        $request = $prediction['request'];
    }
    if (isset($payload['target']) && is_array($payload['target'])) {
        $target = $payload['target'];
    }
    if (isset($prediction['sources']) && is_array($prediction['sources'])) {
        $sources = $prediction['sources'];
    }
    if (isset($prediction['phenomena']) && is_array($prediction['phenomena'])) {
        $phenomena = $prediction['phenomena'];
    }
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

$probability_band_class = static function ($value) {
    if (!is_numeric($value)) {
        return 'atmos-optics-probability--none';
    }

    $probability = max(0.0, min(1.0, (float) $value));
    if ($probability < 0.2) {
        return 'atmos-optics-probability--none';
    }
    if ($probability < 0.4) {
        return 'atmos-optics-probability--blue';
    }
    if ($probability < 0.6) {
        return 'atmos-optics-probability--green';
    }
    if ($probability < 0.8) {
        return 'atmos-optics-probability--orange';
    }
    return 'atmos-optics-probability--red';
};

$render_probability_chip = static function ($value) use ($format_percent, $probability_band_class) {
    $classes = 'atmos-optics-probability ' . $probability_band_class($value);

    return '<span class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '">' .
        htmlspecialchars($format_percent($value), ENT_QUOTES, 'UTF-8') .
        '</span>';
};

$render_current_probability = static function ($value, $reason) use ($render_probability_chip) {
    $chip = $render_probability_chip($value);
    if (!is_string($reason) || trim($reason) === '') {
        return $chip;
    }

    $reason_text = trim($reason);

    return '<span class="atmos-optics-current-cell">' .
        '<span class="atmos-optics-current-trigger" tabindex="0" aria-label="' .
        htmlspecialchars($reason_text, ENT_QUOTES, 'UTF-8') .
        '">' .
        $chip .
        '</span>' .
        '<span class="atmos-optics-reason-tooltip" role="tooltip">' .
        htmlspecialchars($reason_text, ENT_QUOTES, 'UTF-8') .
        '</span>' .
        '</span>';
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

$mode = '';
$prediction_time = '';
$target_name = '';
$latitude = null;
$longitude = null;

if (isset($request['mode']) && is_string($request['mode'])) {
    $mode = $request['mode'];
} elseif (isset($target['mode']) && is_string($target['mode'])) {
    $mode = $target['mode'];
}
if (isset($request['prediction_time']) && is_string($request['prediction_time'])) {
    $prediction_time = $request['prediction_time'];
}
if (isset($target['name']) && is_string($target['name'])) {
    $target_name = $target['name'];
}
if (isset($request['location']) && is_array($request['location'])) {
    if (isset($request['location']['lat']) && is_numeric($request['location']['lat'])) {
        $latitude = (float) $request['location']['lat'];
    }
    if (isset($request['location']['lon']) && is_numeric($request['location']['lon'])) {
        $longitude = (float) $request['location']['lon'];
    }
}
if (($latitude === null || $longitude === null) && isset($target['location']) && is_array($target['location'])) {
    if ($latitude === null && isset($target['location']['lat']) && is_numeric($target['location']['lat'])) {
        $latitude = (float) $target['location']['lat'];
    }
    if ($longitude === null && isset($target['location']['lon']) && is_numeric($target['location']['lon'])) {
        $longitude = (float) $target['location']['lon'];
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
    if ($target_name !== '') {
        $details[] = 'Target: ' . htmlspecialchars($target_name, ENT_QUOTES, 'UTF-8');
    }
    if ($mode !== '') {
        $details[] = 'Mode: ' . htmlspecialchars(ucfirst($mode), ENT_QUOTES, 'UTF-8');
    }
    if ($prediction_time !== '') {
        $details[] = 'Prediction time: ' . htmlspecialchars($format_timestamp($prediction_time), ENT_QUOTES, 'UTF-8');
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
  <?php if ($phenomena === array()): ?>
  <p>No phenomena were included in the current payload.</p>
  <?php else: ?>
  <div class="table-wrap">
  <table class="table1 atmos-optics-table">
    <thead>
      <tr>
        <th>Phenomenon</th>
        <th>Current</th>
        <th>Confidence</th>
        <th>Timeline</th>
      </tr>
    </thead>
    <tbody>
      <?php $row_index = 0; ?>
      <?php foreach ($phenomena as $entry): ?>
      <?php
      if (!is_array($entry)) {
          continue;
      }
      $row_class = ($row_index % 2 === 0) ? 'td0' : 'td1';
      $row_index++;
      $label = '';
      $current_probability = null;
      $confidence = null;
      $timeline_summary = '';
      $reason = '';
      if (isset($entry['label']) && is_string($entry['label'])) {
          $label = $entry['label'];
      } elseif (isset($entry['id']) && is_string($entry['id'])) {
          $label = ucwords(str_replace(array('_', '-'), ' ', $entry['id']));
      }
      if (isset($entry['current']) && is_array($entry['current'])) {
          if (isset($entry['current']['probability']) && is_numeric($entry['current']['probability'])) {
              $current_probability = (float) $entry['current']['probability'];
          }
          if (isset($entry['current']['confidence']) && is_numeric($entry['current']['confidence'])) {
              $confidence = (float) $entry['current']['confidence'];
          }
          if (isset($entry['current']['reason']) && is_string($entry['current']['reason'])) {
              $reason = $entry['current']['reason'];
          }
      }
      if (isset($entry['timeline']) && is_array($entry['timeline'])) {
          $timeline_parts = array();
          foreach ($entry['timeline'] as $timeline_entry) {
              if (!is_array($timeline_entry)) {
                  continue;
              }
              if (!isset($timeline_entry['label']) || !is_string($timeline_entry['label'])) {
                  continue;
              }
              if ($timeline_entry['label'] === 'now') {
                  continue;
              }
              if (!isset($timeline_entry['probability']) || !is_numeric($timeline_entry['probability'])) {
                  continue;
              }
              $timeline_parts[] =
                  '<span class="atmos-optics-timeline-entry">' .
                  '<span class="atmos-optics-timeline-label">' . htmlspecialchars($timeline_entry['label'], ENT_QUOTES, 'UTF-8') . '</span>' .
                  $render_probability_chip($timeline_entry['probability']) .
                  '</span>';
          }
          $timeline_summary = implode('<span class="atmos-optics-timeline-separator" aria-hidden="true">|</span>', $timeline_parts);
      }
      ?>
      <tr>
        <td class="<?php echo $row_class; ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="<?php echo $row_class; ?>"><?php echo $render_current_probability($current_probability, $reason); ?></td>
        <td class="<?php echo $row_class; ?>"><?php echo htmlspecialchars($format_percent($confidence), ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="<?php echo $row_class; ?>"><?php echo $timeline_summary; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</section>
<?php if ($payload !== null): ?>
<section class="panel">
  <h2 class="panel-title">Data Sources</h2>
  <?php if ($sources === array()): ?>
  <p>No source metadata was included in the current payload.</p>
  <?php else: ?>
  <ul>
    <?php foreach ($sources as $source): ?>
    <?php
    if (!is_array($source)) {
        continue;
    }
    $label = '';
    $kind = '';
    $timestamp = '';
    if (isset($source['label']) && is_string($source['label'])) {
        $label = $source['label'];
    } elseif (isset($source['id']) && is_string($source['id'])) {
        $label = ucwords(str_replace(array('_', '-'), ' ', $source['id']));
    }
    if (isset($source['kind']) && is_string($source['kind'])) {
        $kind = $source['kind'];
    }
    if (isset($source['timestamp']) && is_string($source['timestamp'])) {
        $timestamp = $source['timestamp'];
    }
    $parts = array();
    if ($label !== '') {
        $parts[] = $label;
    }
    if ($kind !== '') {
        $parts[] = '(' . $kind . ')';
    }
    if ($timestamp !== '') {
        $parts[] = $timestamp;
    }
    if ($parts === array()) {
        continue;
    }
    ?>
    <li><?php echo htmlspecialchars(implode(' ', $parts), ENT_QUOTES, 'UTF-8'); ?></li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</section>
<?php endif; ?>
<?php include 'tail.php'; ?>
</body>
</html>
