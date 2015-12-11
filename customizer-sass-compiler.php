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

include('classes/settings-page.class.php');
include('classes/customizer-options.class.php');

$styleIncludeOptions = get_option('csp_styles_include', '');
if($styleIncludeOptions != '') {
    foreach ($styleIncludeOptions as $key => $value) {
        if($value){
            include('classes/customizer-options-'.$key.'.class.php');
        }
    }
}

?>