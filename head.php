<?php
ini_set('date.timezone', 'America/Chicago');

if (!isset($page_title) || $page_title === '') {
    $script_name = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
    $script_name = preg_replace('/\.php$/', '', $script_name);
    $script_name = str_replace(['.', '_', '-'], ' ', $script_name);
    $page_title = ucwords($script_name === 'index' ? 'Home' : $script_name);
}
?>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="pragma" content="no-cache" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#07131d">
<title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?> | Astro</title>
<style>
<?php include('my.css'); ?>
</style>
<script>
(() => {
  const collator = new Intl.Collator(undefined, { numeric: true, sensitivity: 'base' });

  function normalizeText(value) {
    return String(value || '').replace(/\s+/g, ' ').trim();
  }

  function numericValue(value) {
    const cleaned = normalizeText(value)
      .replace(/,/g, '')
      .replace(/°/g, '')
      .replace(/%/g, '');

    return /^[+-]?\d+(\.\d+)?$/.test(cleaned) ? Number(cleaned) : null;
  }

  function timeValue(value) {
    const match = normalizeText(value).match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/);
    if (!match) {
      return null;
    }

    return Number(match[1]) * 3600 + Number(match[2]) * 60 + Number(match[3] || 0);
  }

  function sortableValue(cell) {
    const rawValue = cell.dataset.sortValue || cell.textContent || '';
    const text = normalizeText(rawValue);
    if (!text) {
      return { type: 'empty', value: '' };
    }

    const asNumber = numericValue(text);
    if (asNumber !== null) {
      return { type: 'number', value: asNumber };
    }

    const asTime = timeValue(text);
    if (asTime !== null) {
      return { type: 'number', value: asTime };
    }

    const asDate = Date.parse(text);
    if (!Number.isNaN(asDate)) {
      return { type: 'number', value: asDate };
    }

    return { type: 'text', value: text.toLowerCase() };
  }

  function compareValues(left, right) {
    if (left.type === 'empty' && right.type === 'empty') {
      return 0;
    }
    if (left.type === 'empty') {
      return 1;
    }
    if (right.type === 'empty') {
      return -1;
    }
    if (left.type === 'number' && right.type === 'number') {
      return left.value - right.value;
    }

    return collator.compare(String(left.value), String(right.value));
  }

  function refreshRowBands(table) {
    const body = table.tBodies[0];
    if (!body) {
      return;
    }

    Array.from(body.rows).forEach((row, index) => {
      const nextClass = `td${(index + 1) % 2}`;
      Array.from(row.cells).forEach((cell) => {
        cell.classList.remove('td0', 'td1');
        cell.classList.add(nextClass);
      });
    });
  }

  function attachSorting(table) {
    const body = table.tBodies[0];
    if (!body) {
      return;
    }

    const headers = table.querySelectorAll('thead th');
    const buttons = table.querySelectorAll('.sort-button');
    if (!headers.length || !buttons.length) {
      return;
    }

    Array.from(body.rows).forEach((row, index) => {
      row.dataset.originalIndex = String(index);
    });
    refreshRowBands(table);

    buttons.forEach((button, columnIndex) => {
      button.addEventListener('click', () => {
        const direction = button.dataset.direction === 'asc' ? 'desc' : 'asc';
        const rows = Array.from(body.rows);

        rows.sort((rowA, rowB) => {
          const cellA = rowA.cells[columnIndex];
          const cellB = rowB.cells[columnIndex];
          const result = compareValues(sortableValue(cellA), sortableValue(cellB));
          if (result !== 0) {
            return direction === 'asc' ? result : -result;
          }

          return Number(rowA.dataset.originalIndex) - Number(rowB.dataset.originalIndex);
        });

        rows.forEach((row) => body.appendChild(row));
        refreshRowBands(table);
        buttons.forEach((item) => {
          item.dataset.direction = 'none';
        });
        headers.forEach((header) => header.setAttribute('aria-sort', 'none'));
        button.dataset.direction = direction;
        headers[columnIndex].setAttribute('aria-sort', direction === 'asc' ? 'ascending' : 'descending');
      });
    });
  }

  function attachScrollSync(elements) {
    if (!elements || elements.length < 2) {
      return;
    }

    let syncing = false;

    elements.forEach((element) => {
      element.addEventListener('scroll', () => {
        if (syncing) {
          return;
        }

        syncing = true;
        const maxScrollLeft = element.scrollWidth - element.clientWidth;
        const ratio = maxScrollLeft > 0 ? element.scrollLeft / maxScrollLeft : 0;

        elements.forEach((peer) => {
          if (peer === element) {
            return;
          }

          const peerMaxScrollLeft = peer.scrollWidth - peer.clientWidth;
          peer.scrollLeft = peerMaxScrollLeft > 0 ? ratio * peerMaxScrollLeft : 0;
        });

        requestAnimationFrame(() => {
          syncing = false;
        });
      }, { passive: true });
    });
  }

  function layoutResponsiveStages() {
    document.querySelectorAll('.responsive-stage[data-stage-width][data-stage-height]').forEach((stage) => {
      const frame = stage.closest('.responsive-stage-frame');
      if (!frame) {
        return;
      }

      const stageWidth = Number(stage.dataset.stageWidth);
      const stageHeight = Number(stage.dataset.stageHeight);
      if (!stageWidth || !stageHeight) {
        return;
      }

      const host = frame.parentElement || frame;
      const availableWidth = host.clientWidth || stageWidth;
      const scale = Math.min(1, availableWidth / stageWidth);

      frame.style.width = `${stageWidth * scale}px`;
      frame.style.height = `${stageHeight * scale}px`;
      stage.style.width = `${stageWidth}px`;
      stage.style.height = `${stageHeight}px`;
      stage.style.transform = `scale(${scale})`;
    });
  }

  function wrapLegacyTables() {
    document.querySelectorAll('.legacy-html-panel').forEach((panel) => {
      panel.querySelectorAll('table').forEach((table) => {
        if (table.closest('.table-wrap')) {
          return;
        }

        const ancestorTable = table.parentElement ? table.parentElement.closest('table') : null;
        if (ancestorTable) {
          return;
        }

        const wrap = document.createElement('div');
        wrap.className = 'table-wrap table-wrap--legacy';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
      });
    });
  }

  function layoutResponsiveTables() {
    const shouldScale = window.matchMedia('(max-width: 640px)').matches;

    document.querySelectorAll('.table-wrap').forEach((wrap) => {
      const table = Array.from(wrap.children).find((child) => child.tagName === 'TABLE');
      if (!table) {
        return;
      }

      wrap.classList.remove('table-wrap--scaled');
      wrap.style.height = '';
      table.style.width = '';
      table.style.maxWidth = '';
      table.style.transform = '';
      table.style.transformOrigin = '';

      if (!shouldScale) {
        return;
      }

      const availableWidth = wrap.clientWidth;
      const naturalWidth = table.scrollWidth;
      if (!availableWidth || !naturalWidth || naturalWidth <= availableWidth) {
        return;
      }

      const scale = availableWidth / naturalWidth;

      table.style.width = `${naturalWidth}px`;
      table.style.maxWidth = 'none';
      const naturalHeight = table.getBoundingClientRect().height;
      table.style.transformOrigin = 'top left';
      table.style.transform = `scale(${scale})`;
      wrap.style.height = `${Math.ceil(naturalHeight * scale)}px`;
      wrap.classList.add('table-wrap--scaled');
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('table.sortable').forEach(attachSorting);
    wrapLegacyTables();

    const scrollSyncGroups = new Map();
    document.querySelectorAll('[data-scroll-sync-group]').forEach((element) => {
      const groupName = element.getAttribute('data-scroll-sync-group');
      if (!groupName) {
        return;
      }
      if (!scrollSyncGroups.has(groupName)) {
      scrollSyncGroups.set(groupName, []);
      }
      scrollSyncGroups.get(groupName).push(element);
    });
    scrollSyncGroups.forEach((elements) => attachScrollSync(elements));
    layoutResponsiveStages();
    layoutResponsiveTables();
  });

  window.addEventListener('load', () => {
    layoutResponsiveStages();
    layoutResponsiveTables();
  });
  window.addEventListener('resize', () => {
    layoutResponsiveStages();
    layoutResponsiveTables();
  }, { passive: true });
})();
</script>
</head>
