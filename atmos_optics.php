<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
<?php include 'menu.php'; ?>
<?php
$datasets = array(
    array(
        'title' => 'Sun Optics',
        'json_path' => __DIR__ . '/table/atmospheric_optics.json',
        'json_href' => '/table/atmospheric_optics.json',
        'fallback_illumination' => 'solar',
    ),
    array(
        'title' => 'Moon Optics',
        'json_path' => __DIR__ . '/table/atmospheric_optics_lunar.json',
        'json_href' => '/table/atmospheric_optics_lunar.json',
        'fallback_illumination' => 'lunar',
    ),
);

$format_percent = static function ($value) {
    if (!is_numeric($value)) {
        return '0.0%';
    }
    return number_format((float) $value * 100, 1) . '%';
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

$render_probability_chip = static function ($value, $force_none = false) use ($format_percent, $probability_band_class) {
    $band_class = $force_none ? 'atmos-optics-probability--none' : $probability_band_class($value);
    $classes = 'atmos-optics-probability ' . $band_class;

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

$normalize_payload = static function ($payload, $fallback_illumination) {
    $prediction = array();
    $request = array();
    $target = array();
    $sources = array();
    $phenomena = array();
    $mode = '';
    $illumination = $fallback_illumination;
    $prediction_time = '';
    $target_name = '';
    $latitude = null;
    $longitude = null;

    if (isset($payload['prediction']) && is_array($payload['prediction'])) {
        $prediction = $payload['prediction'];
    } elseif (isset($payload['request']) && is_array($payload['request'])) {
        $prediction = $payload;
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
    if (isset($request['mode']) && is_string($request['mode'])) {
        $mode = $request['mode'];
    } elseif (isset($target['mode']) && is_string($target['mode'])) {
        $mode = $target['mode'];
    }
    if (isset($request['prediction_time']) && is_string($request['prediction_time'])) {
        $prediction_time = $request['prediction_time'];
    }
    if (isset($request['options']) && is_array($request['options']) && isset($request['options']['illumination']) && is_string($request['options']['illumination'])) {
        $illumination = $request['options']['illumination'];
    } elseif (isset($target['illumination']) && is_string($target['illumination'])) {
        $illumination = $target['illumination'];
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

    return array(
        'request' => $request,
        'target' => $target,
        'sources' => $sources,
        'phenomena' => $phenomena,
        'mode' => $mode,
        'illumination' => $illumination,
        'prediction_time' => $prediction_time,
        'target_name' => $target_name,
        'latitude' => $latitude,
        'longitude' => $longitude,
    );
};

$loaded_datasets = array();
foreach ($datasets as $dataset) {
    $payload = null;
    $error_message = '';
    if (is_file($dataset['json_path'])) {
        $json = file_get_contents($dataset['json_path']);
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

    $loaded_datasets[] = array_merge(
        $dataset,
        array(
            'payload' => $payload,
            'error_message' => $error_message,
            'normalized' => is_array($payload) ? $normalize_payload($payload, $dataset['fallback_illumination']) : array(),
        )
    );
}
?>
<section class="panel">
  <h1 class="panel-title">Atmospheric Optics</h1>
  <p class="page-note">Current sunlit and moonlit optics predictions for the configured site.</p>
</section>
<?php foreach ($loaded_datasets as $dataset): ?>
<?php
$payload = $dataset['payload'];
$normalized = $dataset['normalized'];
$maps_href = '';
if (is_array($payload) && $normalized['latitude'] !== null && $normalized['longitude'] !== null) {
    $maps_href = 'https://maps.google.com/maps?q=' . $normalized['latitude'] . ',' . $normalized['longitude'];
}
?>
<section class="panel">
  <h2 class="panel-title"><?php echo htmlspecialchars($dataset['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
  <?php if (!is_array($payload)): ?>
  <p><?php echo htmlspecialchars($dataset['error_message'], ENT_QUOTES, 'UTF-8'); ?></p>
  <?php else: ?>
    <?php
    $details = array();
    if ($normalized['target_name'] !== '') {
        $details[] = 'Target: ' . htmlspecialchars($normalized['target_name'], ENT_QUOTES, 'UTF-8');
    }
    if ($normalized['mode'] !== '') {
        $details[] = 'Mode: ' . htmlspecialchars(ucfirst($normalized['mode']), ENT_QUOTES, 'UTF-8');
    }
    if ($normalized['illumination'] !== '') {
        $details[] = 'Illumination: ' . htmlspecialchars(ucfirst($normalized['illumination']), ENT_QUOTES, 'UTF-8');
    }
    if ($normalized['prediction_time'] !== '') {
        $details[] = 'Prediction time: ' . htmlspecialchars($format_timestamp($normalized['prediction_time']), ENT_QUOTES, 'UTF-8');
    }
    ?>
  <p class="weather-card__meta" style="margin:0 0 2px 0;"><?php echo implode(' | ', $details); ?></p>
  <p class="weather-card__meta" style="margin:0 0 5px 0;">
    <?php if ($maps_href !== ''): ?>
    <a href="<?php echo htmlspecialchars($maps_href, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars(number_format((float) $normalized['latitude'], 2) . ', ' . number_format((float) $normalized['longitude'], 2), ENT_QUOTES, 'UTF-8'); ?></a>
    <span class="weather-card__dot" aria-hidden="true">&bull;</span>
    <?php endif; ?>
    <a href="<?php echo htmlspecialchars($dataset['json_href'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">Raw JSON</a>
  </p>
  <?php if ($normalized['phenomena'] === array()): ?>
  <p>No phenomena were included in this payload.</p>
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
      <?php foreach ($normalized['phenomena'] as $entry): ?>
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
        <td class="<?php echo $row_class; ?>"><?php echo $render_probability_chip($confidence, true); ?></td>
        <td class="<?php echo $row_class; ?>"><?php echo $timeline_summary; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
  <?php if ($normalized['sources'] !== array()): ?>
  <p class="page-note" style="margin-top:10px;">
    Sources:
    <?php
    $source_parts = array();
    foreach ($normalized['sources'] as $source) {
        if (!is_array($source)) {
            continue;
        }
        $source_label = '';
        if (isset($source['label']) && is_string($source['label'])) {
            $source_label = $source['label'];
        } elseif (isset($source['id']) && is_string($source['id'])) {
            $source_label = ucwords(str_replace(array('_', '-'), ' ', $source['id']));
        }
        $source_timestamp = '';
        if (isset($source['timestamp']) && is_string($source['timestamp'])) {
            $source_timestamp = $source['timestamp'];
        }
        $source_parts[] = trim($source_label . ' ' . $source_timestamp);
    }
    echo htmlspecialchars(implode(', ', $source_parts), ENT_QUOTES, 'UTF-8');
    ?>
  </p>
  <?php endif; ?>
  <?php endif; ?>
</section>
<?php endforeach; ?>
<?php include 'tail.php'; ?>
</body>
</html>
