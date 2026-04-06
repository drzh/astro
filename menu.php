<?php
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
        if ($disabled) {
            return '<span class="menu-disabled del">' . $label . '</span>';
        }

        $classes = trim('menu-link ' . $extra_class . (astro_is_active($href) ? ' is-active' : ''));
        $current = astro_is_active($href) ? ' aria-current="page"' : '';

        return '<a class="' . $classes . '" href="' . $href . '"' . $current . '>' . $label . '</a>';
    }
}

$updated_label = date('D, Y-n-j, G:i T') . ' [' . gmdate('G:i') . ' UTC]';
?>
<div class="site-shell">
  <header class="site-header site-header--compact">
    <div class="site-meta"><?php echo htmlspecialchars($updated_label, ENT_QUOTES, 'UTF-8'); ?></div>
    <nav class="site-nav" aria-label="Primary">
      <div class="menu-row">
        <?php
        $row = [];
        $row[] = astro_menu_item('/index.php', 'Site');
        $row[] = astro_menu_item('/skycover.php', 'SkyCover', file_exists('config/tgsky.off') || file_exists('../config/tgsky.off'));
        $row[] = astro_menu_item('/skycoverus.php', 'SkyCoverUS', file_exists('config/tgskyus.off') || file_exists('../config/tgskyus.off'));
        $row[] = astro_menu_item('/ndfd.php', 'NDFD');
        $row[] = astro_menu_item('/cloud.php', 'Cloud');
        $row[] = astro_menu_item('/goes.php', 'GOES', file_exists('config/tggoes.off') || file_exists('../config/tggoes.off'));
        $row[] = astro_menu_item('/sfa.php', 'SFA');
        $row[] = astro_menu_item('/radar.php', 'Radar');
        echo implode('<span class="menu-divider" aria-hidden="true">|</span>', $row);
        ?>
      </div>
      <div class="menu-row">
        <?php
        $row = [];
        $row[] = astro_menu_item('/nam60.php', 'NAM-60', file_exists('config/tgnam60.off') || file_exists('../config/tgnam60.off'), 'alert');
        $row[] = astro_menu_item('/nam84.php', 'NAM-84', file_exists('config/tgnam84.off') || file_exists('../config/tgnam84.off'));
        $row[] = astro_menu_item('/nam84p.php', 'NAM-84-P');
        $row[] = astro_menu_item('/nam240p.php', 'NAM-240-P');
        $row[] = astro_menu_item('/nam384p.php', 'NAM-384-P');
        echo implode('<span class="menu-divider" aria-hidden="true">|</span>', $row);
        ?>
      </div>
      <div class="menu-row">
        <?php
        $row = [];
        $row[] = astro_menu_item('/daynight.php', 'Night');
        $row[] = astro_menu_item('/light_pollution/lp.php', 'LPollution');
        $row[] = astro_menu_item('/planets.php', 'Planets');
        $row[] = astro_menu_item('/table/table.php?tb=occultation.txt', 'Occultation');
        $row[] = astro_menu_item('/table/table.php?tb=cobs.commet.list.observed.json.txt', 'Comets');
        $row[] = astro_menu_item('/aurora.php', 'Aurora');
        $row[] = astro_menu_item('/sun.php', 'Sun');
        echo implode('<span class="menu-divider" aria-hidden="true">|</span>', $row);
        ?>
      </div>
      <div class="menu-row">
        <?php
        $row = [];
        $row[] = astro_menu_item('/satellite_ha.php?sat=All&mag=3&max=20', 'Sate (Vis)');
        $row[] = astro_menu_item('/satellite.php?sat=ALL_PRI&max=20', 'Sate (Ham)');
        $row[] = astro_menu_item('/ham.php', 'HAM');
        $row[] = astro_menu_item('/table/table.php?tb=radnet.ft_worth.txt', 'Radiation');
        $row[] = astro_menu_item('/economy.php', 'Eco');
        $row[] = astro_menu_item('/map.php', 'Maps');
        $row[] = astro_menu_item('/link.php', 'Links');
        $row[] = astro_menu_item('/about.php', 'About');
        echo implode('<span class="menu-divider" aria-hidden="true">|</span>', $row);
        ?>
      </div>
    </nav>
  </header>
  <main class="site-main">
    <section class="page-section page-content">
