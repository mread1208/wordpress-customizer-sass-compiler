<?php

$wpcsc_settings_page = new WpCscSettingsPage();

add_filter('plugin_action_links', 'wpcsc_plugin_action_links', 10, 2);

function wpcsc_plugin_action_links($links, $file) {
    static $wpcsc_plugin;
    if( !$wpcsc_plugin ) {
        $wpcsc_plugin = plugin_basename(__FILE__);
    }
    if ($file == $wpcsc_plugin) {
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=csc-plugin-settings">Settings</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

add_action('admin_enqueue_scripts', 'wpcsc_enqueue_scripts', 50);

function wpcsc_enqueue_scripts() {
    wp_register_script('wpscs-options', WPCSC_PLUGIN_URL .'/assets/js/wpcsc-options.js', array('jquery'));
    wp_enqueue_script('wpscs-options');
    
    wp_register_style('wpscs-options-styles', WPCSC_PLUGIN_URL .'/assets/css/wpscs-options-styles.css');
    wp_enqueue_style('wpscs-options-styles');
}

?>