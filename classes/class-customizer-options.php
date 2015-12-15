<?php 

class WpCscCustomizerOptions {
   
    /**
    * Compiling preferences properites
    *
    * @var string
    * @access public
    */

    public $scss_dir, $css_file, $sass_vars, $sass_import_file, $compile_method, $scssc, $compile_errors;
    
    /**
    * Set values for WpCscCustomizerOptions::properties
    *
    * @var array compile_errors - catches errors from compile
    */
    
    public function __construct () {
        $this->compile_errors = array();
    }
    
    public function run_compiler($scss_dir, $css_file, $sass_vars, $sass_import_file, $compile_method = 'scss_formatter_nested') {
        
        require_once(WPCSC_PLUGIN_DIR.'/scssphp/scss.inc.php');
        $scss = new scssc();
        $scss->setImportPaths($scss_dir);
        $scss->setFormatter($compile_method);
        $scss->setVariables($sass_vars);
        $new_css = $scss->compile($sass_import_file);
        
        if (is_writable($scss_dir)) {
            $current_css = file_get_contents($css_file);
            file_put_contents($css_file, $new_css);
        } else {
            $errors = array(
                'file' => 'CSS Directory',
                'message' => "File Permissions Error, permission denied. Please make the plugin CSS directory writable."
            );
            array_push($this->compile_errors, $errors);
        }
        
    }
    
}

add_action( 'init' , 'csc_customizer_init' );

function csc_customizer_init() {
    $csc_customizer_options = new WpCscCustomizerOptions();
}

?>