<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
<?php include 'menu.php'; ?>
<?php
$datasets = array(
    array(
        'title' => 'Sun Optics',
        'json_path' => __DIR__ . '/table/data/atmospheric_optics_solar.json',
        'json_href' => '/table/data/atmospheric_optics_solar.json',
        'fallback_illumination' => 'solar',
    ),
    array(
        'title' => 'Moon Optics',
        'json_path' => __DIR__ . '/table/data/atmospheric_optics_lunar.json',
        'json_href' => '/table/data/atmospheric_optics_lunar.json',
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

    return '<span class="atmos-optics-current-cell" data-atmos-optics-reason="' .
        htmlspecialchars($reason_text, ENT_QUOTES, 'UTF-8') .
        '">' .
        '<span class="atmos-optics-current-trigger" tabindex="0" aria-label="' .
        htmlspecialchars($reason_text, ENT_QUOTES, 'UTF-8') .
        '">' .
        $chip .
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

$format_altitude = static function ($value) {
    if (!is_numeric($value)) {
        return '';
    }

    return number_format((float) $value, 1) . ' deg';
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
    $celestial = array();
    $active_body_label = '';
    $active_body_altitude = null;

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
    if (isset($payload['celestial']) && is_array($payload['celestial'])) {
        $celestial = $payload['celestial'];
    } elseif (isset($prediction['celestial']) && is_array($prediction['celestial'])) {
        $celestial = $prediction['celestial'];
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
    if ($illumination === 'solar' && isset($celestial['sun']) && is_array($celestial['sun'])) {
        $active_body_label = 'Sun';
        if (isset($celestial['sun']['altitude']) && is_numeric($celestial['sun']['altitude'])) {
            $active_body_altitude = (float) $celestial['sun']['altitude'];
        }
    } elseif ($illumination === 'lunar' && isset($celestial['moon']) && is_array($celestial['moon'])) {
        $active_body_label = 'Moon';
        if (isset($celestial['moon']['altitude']) && is_numeric($celestial['moon']['altitude'])) {
            $active_body_altitude = (float) $celestial['moon']['altitude'];
        }
    } elseif (isset($celestial['sun']) && is_array($celestial['sun']) && isset($celestial['sun']['altitude']) && is_numeric($celestial['sun']['altitude'])) {
        $active_body_label = 'Sun';
        $active_body_altitude = (float) $celestial['sun']['altitude'];
    } elseif (isset($celestial['moon']) && is_array($celestial['moon']) && isset($celestial['moon']['altitude']) && is_numeric($celestial['moon']['altitude'])) {
        $active_body_label = 'Moon';
        $active_body_altitude = (float) $celestial['moon']['altitude'];
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
        'active_body_label' => $active_body_label,
        'active_body_altitude' => $active_body_altitude,
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
    if ($normalized['prediction_time'] !== '') {
        $details[] = 'Prediction time: ' . htmlspecialchars($format_timestamp($normalized['prediction_time']), ENT_QUOTES, 'UTF-8');
    }
    if ($normalized['active_body_label'] !== '' && $normalized['active_body_altitude'] !== null) {
        $details[] = htmlspecialchars($normalized['active_body_label'], ENT_QUOTES, 'UTF-8') .
            ' altitude: ' .
            htmlspecialchars($format_altitude($normalized['active_body_altitude']), ENT_QUOTES, 'UTF-8');
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
  <?php
  $timeline_labels = array();
  foreach ($normalized['phenomena'] as $timeline_entry_source) {
      if (!is_array($timeline_entry_source) || !isset($timeline_entry_source['timeline']) || !is_array($timeline_entry_source['timeline'])) {
          continue;
      }
      foreach ($timeline_entry_source['timeline'] as $timeline_entry) {
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
          if (!array_key_exists($timeline_entry['label'], $timeline_labels)) {
              $timeline_labels[$timeline_entry['label']] = $timeline_entry['label'];
          }
      }
  }
  ?>
  <div class="table-wrap atmos-optics-table-wrap">
  <table class="table1 atmos-optics-table">
    <thead>
      <tr>
        <th>Phenomenon</th>
        <th>Current</th>
        <?php foreach ($timeline_labels as $timeline_label): ?>
        <th class="atmos-optics-timeline-heading"><?php echo htmlspecialchars($timeline_label, ENT_QUOTES, 'UTF-8'); ?></th>
        <?php endforeach; ?>
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
      $timeline_values = array();
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
          if (isset($entry['current']['reason']) && is_string($entry['current']['reason'])) {
              $reason = $entry['current']['reason'];
          }
      }
      if (isset($entry['timeline']) && is_array($entry['timeline'])) {
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
              $timeline_values[$timeline_entry['label']] = $timeline_entry['probability'];
          }
      }
      ?>
      <tr>
        <td class="<?php echo $row_class; ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="<?php echo $row_class; ?> atmos-optics-current-column"><?php echo $render_current_probability($current_probability, $reason); ?></td>
        <?php foreach ($timeline_labels as $timeline_label): ?>
        <td class="<?php echo $row_class; ?> atmos-optics-timeline-cell"><?php echo array_key_exists($timeline_label, $timeline_values) ? $render_probability_chip($timeline_values[$timeline_label]) : ''; ?></td>
        <?php endforeach; ?>
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
<script>
(() => {
  const tooltip = document.createElement('div');
  tooltip.className = 'atmos-optics-pointer-tooltip';
  tooltip.setAttribute('role', 'tooltip');
  document.body.appendChild(tooltip);

  function moveAtmosOpticsTooltip(cell, pointerEvent) {
    const trigger = cell.querySelector('.atmos-optics-current-trigger');
    const reason = cell.dataset.atmosOpticsReason || (trigger ? trigger.getAttribute('aria-label') : '');
    if (!trigger || !reason) {
      return;
    }

    const padding = 8;
    const pointerGap = 12;
    const anchor = pointerEvent && typeof pointerEvent.clientX === 'number'
      ? { x: pointerEvent.clientX, y: pointerEvent.clientY }
      : (() => {
          const rect = trigger.getBoundingClientRect();
          return { x: rect.right, y: rect.top + rect.height / 2 };
        })();
    tooltip.textContent = reason;
    tooltip.classList.add('is-visible');
    tooltip.style.maxWidth = '';
    const availableWidth = Math.max(96, window.innerWidth - anchor.x - pointerGap - padding);
    tooltip.style.maxWidth = `${availableWidth}px`;
    const tooltipRect = tooltip.getBoundingClientRect();
    let left = anchor.x + pointerGap;
    let top = anchor.y + padding;

    top = Math.max(padding, Math.min(top, window.innerHeight - tooltipRect.height - padding));

    tooltip.style.setProperty('--atmos-tooltip-left', `${Math.round(left)}px`);
    tooltip.style.setProperty('--atmos-tooltip-top', `${Math.round(top)}px`);
  }

  function hideAtmosOpticsTooltip() {
    tooltip.classList.remove('is-visible');
  }

  document.querySelectorAll('.atmos-optics-current-cell').forEach((cell) => {
    cell.addEventListener('pointerenter', (event) => moveAtmosOpticsTooltip(cell, event));
    cell.addEventListener('pointermove', (event) => moveAtmosOpticsTooltip(cell, event));
    cell.addEventListener('focusin', () => moveAtmosOpticsTooltip(cell, null));
    cell.addEventListener('pointerleave', hideAtmosOpticsTooltip);
    cell.addEventListener('focusout', hideAtmosOpticsTooltip);
  });
})();
</script>
<?php include 'tail.php'; ?>
</body>
</html>
