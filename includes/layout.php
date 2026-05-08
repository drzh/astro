<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/ui.php';

if (!function_exists('astro_resolve_page_title')) {
    function astro_resolve_page_title($page_title = null)
    {
        $page_title = trim((string) ($page_title ?? ''));
        if ($page_title !== '') {
            return $page_title;
        }

        $script_name = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
        $script_name = preg_replace('/\.php$/', '', $script_name);
        $script_name = str_replace(array('.', '_', '-'), ' ', $script_name);
        return ucwords($script_name === 'index' ? 'Home' : $script_name);
    }
}

if (!function_exists('astro_render_head')) {
    function astro_render_head($page_title = null)
    {
        $page_title = astro_resolve_page_title($page_title);
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
<?php include astro_path('my.css'); ?>
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
      wrap.style.paddingRight = '';
      table.style.width = '';
      table.style.maxWidth = '';
      table.style.transform = '';
      table.style.transformOrigin = '';

      if (!shouldScale) {
        return;
      }

      const availableWidth = wrap.clientWidth;
      const naturalWidth = Math.ceil(table.offsetWidth || table.scrollWidth);
      if (!availableWidth || !naturalWidth || naturalWidth <= availableWidth) {
        return;
      }

      const safeWidth = Math.max(availableWidth - 2, 1);
      const scale = safeWidth / naturalWidth;

      table.style.width = `${naturalWidth}px`;
      table.style.maxWidth = 'none';
      const naturalHeight = Math.ceil(table.offsetHeight || table.getBoundingClientRect().height);
      table.style.transformOrigin = 'top left';
      table.style.transform = `scale(${scale})`;
      wrap.style.height = `${Math.ceil(naturalHeight * scale) + 1}px`;
      wrap.style.paddingRight = '2px';
      wrap.classList.add('table-wrap--scaled');
    });
  }

  let imageModalController = null;
  let imageModalTrigger = null;

  function getImageModalController() {
    if (imageModalController) {
      return imageModalController;
    }

    const root = document.getElementById('image-modal');
    if (!root) {
      return null;
    }

    imageModalController = {
      root,
      image: root.querySelector('[data-image-modal-image]'),
      caption: root.querySelector('[data-image-modal-caption]'),
      close: root.querySelector('[data-image-modal-close]'),
    };

    return imageModalController;
  }

  function closeImageModal() {
    const modal = getImageModalController();
    if (!modal || modal.root.hidden) {
      return;
    }

    modal.root.hidden = true;
    modal.root.setAttribute('aria-hidden', 'true');
    if (modal.image) {
      modal.image.removeAttribute('src');
      modal.image.alt = '';
    }
    if (modal.caption) {
      modal.caption.hidden = true;
      modal.caption.textContent = '';
    }
    document.body.classList.remove('image-modal-open');

    if (imageModalTrigger && typeof imageModalTrigger.focus === 'function') {
      imageModalTrigger.focus({ preventScroll: true });
    }
    imageModalTrigger = null;
  }

  function openImageModal(trigger) {
    const modal = getImageModalController();
    if (!modal || !trigger || !modal.image) {
      return;
    }

    const source = trigger.getAttribute('href') || trigger.dataset.imageModalSrc || '';
    if (!source) {
      return;
    }

    const inlineImage = trigger.querySelector('img');
    const alt = trigger.dataset.imageModalAlt || (inlineImage ? inlineImage.getAttribute('alt') : '') || '';
    const caption = trigger.dataset.imageModalCaption || '';

    imageModalTrigger = trigger;
    modal.image.src = source;
    modal.image.alt = alt;
    if (modal.caption) {
      modal.caption.textContent = caption;
      modal.caption.hidden = caption === '';
    }

    modal.root.hidden = false;
    modal.root.setAttribute('aria-hidden', 'false');
    document.body.classList.add('image-modal-open');

    if (modal.close) {
      modal.close.focus({ preventScroll: true });
    }
  }

  function attachImageModal() {
    const modal = getImageModalController();
    if (!modal) {
      return;
    }

    document.addEventListener('click', (event) => {
      const trigger = event.target.closest('[data-image-modal]');
      if (trigger) {
        event.preventDefault();
        openImageModal(trigger);
        return;
      }

      if (event.target === modal.root || event.target.closest('[data-image-modal-close]')) {
        event.preventDefault();
        closeImageModal();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        closeImageModal();
      }
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
    attachImageModal();
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
<?php
    }
}

if (!function_exists('astro_is_active')) {
    function astro_is_active($href)
    {
        $request_uri = $_SERVER['REQUEST_URI'] ?? ($_SERVER['SCRIPT_NAME'] ?? '');
        $request_path = parse_url($request_uri, PHP_URL_PATH) ?: '';
        $request_query = parse_url($request_uri, PHP_URL_QUERY) ?: '';
        $target_path = parse_url($href, PHP_URL_PATH) ?: '';
        $target_query = parse_url($href, PHP_URL_QUERY) ?: '';

        if (basename($request_path) !== basename($target_path)) {
            return false;
        }

        if ($target_query === '') {
            return true;
        }

        parse_str($request_query, $current_params);
        parse_str($target_query, $target_params);

        foreach ($target_params as $key => $value) {
            if (!array_key_exists($key, $current_params) || (string) $current_params[$key] !== (string) $value) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('astro_menu_item')) {
    function astro_menu_item($href, $label, $disabled = false, $extra_class = '')
    {
        $label = htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8');
        if ($disabled) {
            return '<span class="menu-disabled del">' . $label . '</span>';
        }

        $active = astro_is_active($href);
        $classes = trim('menu-link ' . $extra_class . ($active ? ' is-active' : ''));
        $current = $active ? ' aria-current="page"' : '';

        return '<a class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '" href="' . htmlspecialchars((string) $href, ENT_QUOTES, 'UTF-8') . '"' . $current . '>' . $label . '</a>';
    }
}

if (!function_exists('astro_feature_disabled')) {
    function astro_feature_disabled($relative_path)
    {
        return file_exists(astro_path($relative_path));
    }
}

if (!function_exists('astro_menu_rows')) {
    function astro_menu_rows()
    {
        return array(
            array(
                array('href' => '/index.php', 'label' => 'Site'),
                array('href' => '/skycover.php', 'label' => 'SkyCover', 'disabled' => astro_feature_disabled('config/tgsky.off')),
                array('href' => '/ndfd.php', 'label' => 'NDFD'),
                array('href' => '/cloud.php', 'label' => 'Cloud'),
                array('href' => '/sfa.php', 'label' => 'SFA'),
                array('href' => '/radar.php', 'label' => 'Radar'),
            ),
            array(
                array('href' => '/nam60.php', 'label' => 'NAM-60', 'disabled' => astro_feature_disabled('config/tgnam60.off'), 'extra_class' => 'alert'),
                array('href' => '/nam84.php', 'label' => 'NAM-84', 'disabled' => astro_feature_disabled('config/tgnam84.off')),
                array('href' => '/nam84p.php', 'label' => 'NAM-84-P'),
                array('href' => '/nam240p.php', 'label' => 'GFS-240'),
                array('href' => '/nam384p.php', 'label' => 'GFS-384'),
            ),
            array(
                array('href' => '/sun.php', 'label' => 'Sun'),
                array('href' => '/aurora.php', 'label' => 'Aurora'),
                array('href' => '/table.php?tb=cobs.commet.list.observed.json.txt', 'label' => 'Comets'),
                array('href' => '/planets.php', 'label' => 'Planets'),
                array('href' => '/table.php?tb=occultation.txt', 'label' => 'Occultation'),
            ),
            array(
                array('href' => '/atmos_optics.php', 'label' => 'Atmos Optics'),
                array('href' => '/daynight.php', 'label' => 'Twilight'),
                array('href' => '/light_pollution/lp.php', 'label' => 'LPollution'),
                array('href' => '/economy.php', 'label' => 'Eco'),
                array('href' => '/map.php', 'label' => 'Maps'),
            ),
            array(
                array('href' => '/table.php?tbm=satellite_vis&sat=All&mag=3&max=20', 'label' => 'Sate (Vis)'),
                array('href' => '/table.php?tbm=satellite_ham&sat=ALL_PRI&max=20', 'label' => 'Sate (Ham)'),
                array('href' => '/ham.php', 'label' => 'HAM'),
                array('href' => '/link.php', 'label' => 'Links'),
                array('href' => '/about.php', 'label' => 'About'),
            ),
        );
    }
}

if (!function_exists('astro_render_menu_row')) {
    function astro_render_menu_row($items)
    {
        $rendered = array();
        foreach ($items as $item) {
            $rendered[] = astro_menu_item(
                $item['href'],
                $item['label'],
                $item['disabled'] ?? false,
                $item['extra_class'] ?? ''
            );
        }

        return implode('<span class="menu-divider" aria-hidden="true">|</span>', $rendered);
    }
}

if (!function_exists('astro_render_menu')) {
    function astro_render_menu()
    {
        $updated_label = date('D, Y-n-j, G:i T') . ' [' . gmdate('G:i') . ' UTC]';
        ?>
<div class="site-shell">
  <header class="site-header site-header--compact">
    <div class="site-meta"><?php echo htmlspecialchars($updated_label, ENT_QUOTES, 'UTF-8'); ?></div>
    <nav class="site-nav" aria-label="Primary">
      <?php foreach (astro_menu_rows() as $row): ?>
      <div class="menu-row">
        <?php echo astro_render_menu_row($row); ?>
      </div>
      <?php endforeach; ?>
    </nav>
  </header>
  <main class="site-main">
    <section class="page-section page-content">
<?php
    }
}

if (!function_exists('astro_render_tail')) {
    function astro_render_tail()
    {
        ?>
    </section>
  </main>
</div>
<div class="image-modal" id="image-modal" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="Expanded image">
  <div class="image-modal__panel" role="document">
    <button type="button" class="image-modal__close" data-image-modal-close>Close</button>
    <figure class="image-modal__figure">
      <img class="image-modal__image" data-image-modal-image alt="">
      <figcaption class="image-modal__caption" data-image-modal-caption hidden></figcaption>
    </figure>
  </div>
</div>
<?php
    }
}
