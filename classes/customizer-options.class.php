<?php 

class CspCustomizerOptions
{
    
    function __construct() {
       
    }
    
    public function run_compiler($sassVariables, $sassCompileFile, $cssFilePath) {
        require_once(plugin_dir_path( __FILE__ ).'../classes/scss.inc.php');
        $scss = new scssc();
        $scss->setImportPaths(plugin_dir_path( __FILE__ ).'../assets/stylesheets/');
        $scss->setFormatter( 'scss_formatter' );
        $scss->setVariables($sassVariables);

        $newCss = $scss->compile($sassCompileFile);
        
        $currentCss = file_get_contents($cssFilePath);
        file_put_contents($cssFilePath, $newCss);
    }
    
}

add_action( 'init' , 'csp_customizer_init' );

function csp_customizer_init() {
    $csp_customizer_options = new CspCustomizerOptions();
}

?>