<?php 

class WpCscCustomizerOptions {
   
    /**
    * Compiling preferences properites
    *
    * @var string
    * @access public
    */

    public $scss_dir, $sass_vars, $sass_import_file, $css_name, $compile_method, $scssc, $compile_errors;
    
    /**
    * Set values for WpCscCustomizerOptions::properties
    *
    * @var array compile_errors - catches errors from compile
    */
    
    public function __construct () {
        $this->compile_errors = array();
    }
    
    public function run_compiler($scss_dir, $sass_vars, $sass_import_file, $css_name, $compile_method = 'scss_formatter_nested') {
        
        require_once(WPCSC_PLUGIN_DIR.'/scssphp/scss.inc.php');
        $scss = new scssc();
        $scss->setImportPaths($scss_dir);
        $scss->setFormatter($compile_method);
        $scss->setVariables($sass_vars);
        $new_css = $scss->compile($sass_import_file);
        
        /* Write the CSS to the Database */
        $wpcscOptions = get_option('wpcsc1208_option_settings');
        /* Sanitze the CSS before going into the Database
        Refer to this doc, http://wptavern.com/wordpress-theme-review-team-sets-new-guidelines-for-custom-css-boxes */
        $wpcscOptions['wpcsc_content'][$css_name] = wp_kses( $new_css, array( '\'', '\"' ) );
        update_option('wpcsc1208_option_settings', $wpcscOptions);
        
        
    }
    
}

add_action( 'init' , 'csc_customizer_init' );

function csc_customizer_init() {
    $csc_customizer_options = new WpCscCustomizerOptions();
}

?>