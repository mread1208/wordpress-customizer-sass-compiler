<?php

/**
 * Plugin Name: Customizer Sass Compiler
 * Plugin URI: http://www.michaelcread.com
 * Description: This plugin adds a Sass compiler to the WordPress customizer.
 * Version: 1.0.0
 * Author: Michael Read
 * Author URI: http://www.michaelcread.com
 * License: ---
 */


/*
 * 1. PLUGIN GLOBAL VARIABLES
 */

if (!defined('WPCSC_THEME_DIR'))
    define('WPCSC_THEME_DIR', get_stylesheet_directory());

if (!defined('WPCSC_PLUGIN_NAME'))
    define('WPCSC_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('WPCSC_PLUGIN_DIR'))
    define('WPCSC_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WPCSC_PLUGIN_NAME);

if (!defined('WPCSC_PLUGIN_URL'))
    define('WPCSC_PLUGIN_URL', WP_PLUGIN_URL . '/' . WPCSC_PLUGIN_NAME);

// Plugin Version
if (!defined('WPCSC_VERSION_NUM'))
    define('WPCSC_VERSION_NUM', '1.0.0');

$wpcscs1208OptionSettings = array('wpcscs_version' => WPCSC_VERSION_NUM, 'wpcsc_content' => array('bootstrap' => '', 'custom' => ''));

// Creates row in DB for all our options page settings
add_option('wpcsc1208_option_settings', $wpcscs1208OptionSettings);
// Creates row in DB for all our customizer page settings
add_option('wpcsc1208_customizer_settings');


/*
 * 2. REQUIRE DEPENDANCIES
 *
 *    scssphp - scss compiler 
 *    class-customizer-options.php - main class for customizer options
 *    
 *    @param $styleIncludeOptions = Get's all the included stylesheets set in the options menu
 *           Loop through all of the included styles and add their respective class.
 *    
 *    customizer-sass-options.php - settings for plugin page
 */

include_once WPCSC_PLUGIN_DIR . '/scssphp/scss.inc.php'; // Sass Compiler
include_once WPCSC_PLUGIN_DIR . '/classes/class-customizer-options.php'; // Class for customizer options

$styleIncludeOptions = get_option('wpcsc1208_option_settings', '');

if(!empty($styleIncludeOptions['wpcsc_styles_include'])) {
    foreach ($styleIncludeOptions['wpcsc_styles_include'] as $key => $value) {
        include(WPCSC_PLUGIN_DIR . '/classes/class-customizer-options-'.$key.'.php');
    }
}

include_once WPCSC_PLUGIN_DIR . '/customizer-sass-options.php'; // Options page class


/**
 * 3. REGISTER SETTINGS
 *
 *  Instantiate Options Page
 *  Create link on plugin page to settings page
 */

if(is_admin()) {
    include_once WPCSC_PLUGIN_DIR . '/includes/admin.php';
} else if( ! is_admin() ) {
    include_once WPCSC_PLUGIN_DIR . '/includes/public.php';
}

?>