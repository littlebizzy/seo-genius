<?php
/*
Plugin Name: SEO Genius
Plugin URI: https://www.littlebizzy.com/plugins/seo-genius
Description: Lightweight WordPress SEO plugin
Version: 1.0.0
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
GitHub Plugin URI: littlebizzy/seo-genius
Primary Branch: master
Tested up to: 6.6
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Disable WordPress.org updates for this plugin
add_filter( 'gu_override_dot_org', function( $overrides ) {
    $overrides[] = 'seo-genius/seo-genius.php';
    return $overrides;
});

// Load settings page
require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';

// Load enabled modules
$modules = get_option('seo_genius_enabled_modules', array());

foreach ($modules as $module) {
    $module_file = plugin_dir_path(__FILE__) . "modules/$module/$module.php";
    if (file_exists($module_file)) {
        require_once $module_file;
    }
}

// Ref: ChatGPT
