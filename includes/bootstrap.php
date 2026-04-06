<?php

require_once __DIR__ . '/paths.php';

if (!defined('ASTRO_TIMEZONE')) {
    define('ASTRO_TIMEZONE', 'America/Chicago');
}

ini_set('date.timezone', ASTRO_TIMEZONE);
date_default_timezone_set(ASTRO_TIMEZONE);
