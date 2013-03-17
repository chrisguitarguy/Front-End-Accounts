<?php
!defined('ABSPATH') && exit;

require_once dirname(__DIR__) . '/common.php';

add_action('wp_enqueue_scripts', 'frontend_accounts_theme_enqueue', 100);
add_action('stylesheet_uri', 'frontend_accounts_replace_stylesheet');
