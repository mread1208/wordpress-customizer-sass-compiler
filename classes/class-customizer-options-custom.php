<?php

class WpCscCustomCustomizerOptions extends WpCscCustomizerOptions
{
    
    private $options;
    
    public function __construct() {
        add_action('customize_register', array($this, 'csc_custom_compiler_options_register'));
    }
    
    public function csc_custom_compiler_options() {
        // Get all variables from the customizer
        $this->options = get_option('wpcsc1208_customizer_settings', array());
        //$sass_vars = $this->options['wpcsc_bootstrap_customizer'];
        
        //$sass_import_file = '@import "_bootstrap.scss";';
        //$scss_dir = WPCSC_PLUGIN_DIR.'/assets/bootstrap/stylesheets/';
        //$css_file = WPCSC_PLUGIN_DIR.'/assets/bootstrap/stylesheets/bootstrap.min.css';
        
        //$this->run_compiler($scss_dir, $css_file, $sass_vars, $sass_import_file);
    }

    public function csc_custom_compiler_options_register($wp_customize){
        
        $this->options = get_option('wpcsc1208_option_settings', array());
        
        if(isset($this->options['wpcsc_custom_options']['custom_sass_variables']) && !empty($this->options['wpcsc_custom_options']['custom_sass_variables'])) {
        
            $wp_customize->add_panel( 'wpcsc1208_custom_options_panel', array(
                    'title'       => __('Custom Sass Options', 'wpcsc1208'),
                    'description' => __('Modify your custom variabels set in the Sass Compiler settings', 'wpcsc1208'),
                    'priority'    => 10,
                )
            );
            
            $wp_customize->add_section( 'custom_variables_section', array(
                    'priority' => 10,
                    'capability' => 'edit_theme_options',
                    'theme_supports' => '',
                    'title' => __( 'Custom Variables', 'wpcsc1208' ),
                    'description' => '',
                    'panel' => 'wpcsc1208_custom_options_panel',
                )
            );
            
            foreach($this->options['wpcsc_custom_options']['custom_sass_variables'] as $custom_sass_option) {
                
                // SETTINGS
                $wp_customize->add_setting(
                    'wpcsc1208_customizer_settings[wpcsc_custom_variables_customizer]['.$custom_sass_option['key'].']', array(
                        'default' => $custom_sass_option['value'],
                        'type' => 'option', 
                        'capability' => 'edit_theme_options',
                        'transport' => 'postMessage',
                        'sanitize_callback' => array($this, 'check_compile_custom_variables')
                    )
                );
                
                // CONTROLS
                $wp_customize->add_control(
                    'wpcsc1208_customizer_settings[wpcsc_custom_variables_customizer]['.$custom_sass_option['key'].']', array(
                        'label' => $custom_sass_option['key'], 
                        'section' => 'custom_variables_section',
                        'settings' => 'wpcsc1208_customizer_settings[wpcsc_custom_variables_customizer]['.$custom_sass_option['key'].']'
                    )
                );
            }
        }
    }

    public function check_compile_custom_variables($vars){
        // Make sure we sanatize this using default WordPress Sanatize functions
        $vars = sanitize_hex_color($vars);

        if(!empty($vars)) {
           //add_action('customize_save_after', array( $this, 'csc_custom_compiler_options'));
        }

        return $vars;
    }
    
}

add_action( 'init' , 'wpcsc1208_customizer_custom_init' );

function wpcsc1208_customizer_custom_init() {
    $csc_customizer_custom_options = new WpCscCustomCustomizerOptions();
}

?>