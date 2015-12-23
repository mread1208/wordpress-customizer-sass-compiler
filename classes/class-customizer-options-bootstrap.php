<?php

class WpCscBootstrapCustomizerOptions extends WpCscCustomizerOptions
{
    
    private $options, $bs_csc_colors, $bs_csc_fonts;
    
    public function __construct() {
        add_action('customize_register', array($this, 'csc_customize_bootstrap_register'));
    }
    
    public function csc_bootstrap_compiler_options() {
        // Get all variables from the customizer
        $this->options = get_option('wpcsc1208_customizer_settings', array());
        $sass_vars = $this->options['wpcsc_bootstrap_customizer'];
        
        $sass_import_file = '@import "_bootstrap.scss";';
        $scss_dir = WPCSC_PLUGIN_DIR.'/assets/bootstrap/stylesheets/';
        $css_file = WPCSC_PLUGIN_DIR.'/assets/bootstrap/stylesheets/bootstrap.min.css';
        
        $this->run_compiler($scss_dir, $css_file, $sass_vars, $sass_import_file);
    }

    public function csc_customize_bootstrap_register($wp_customize){
        
        $this->options = get_option('wpcsc1208_option_settings', array());
        
        if(isset($this->options['wpcsc_bootstrap_options']) && !empty($this->options['wpcsc_bootstrap_options'])) {
        
            $wp_customize->add_panel( 'wpcsc1208_bootstrap_options_panel', array(
                    'title'       => __('Bootstrap Options', 'wpcsc1208'),
                    'description' => __('Modify the existing bootstrap colors', 'wpcsc1208'),
                    'priority'    => 10,
                )
            );
            
            $this->bs_csc_colors = (isset($this->options['wpcsc_bootstrap_options']['color_variables']) ? $this->options['wpcsc_bootstrap_options']['color_variables'] : array());
            $this->bs_csc_fonts = (isset($this->options['wpcsc_bootstrap_options']['font_variables']) ? $this->options['wpcsc_bootstrap_options']['font_variables'] : array());
                
            if(!empty($this->bs_csc_colors)) {
                
                $wp_customize->add_section( 'bootstrap_colors_section', array(
                        'priority' => 10,
                        'capability' => 'edit_theme_options',
                        'theme_supports' => '',
                        'title' => __( 'Bootstrap Colors', 'wpcsc1208' ),
                        'description' => '',
                        'panel' => 'wpcsc1208_bootstrap_options_panel',
                    )
                );
                
                $colors = array();

                if(in_array('body-bg', $this->bs_csc_colors)) {
                    $colors[] = array(
                        'slug'      =>'wpcsc1208_customizer_settings[wpcsc_bootstrap_customizer][body-bg]', 
                        'default'   => '#ffffff',
                        'label'     => 'Background Color',
                        'description' => __( 'The main body background color.', 'wpcsc1208' ),
                    );
                }

                if(in_array ('text-color', $this->bs_csc_colors)) {
                    $colors[] = array(
                        'slug'      =>'wpcsc1208_customizer_settings[wpcsc_bootstrap_customizer][text-color]', 
                        'default'   => '#333333',
                        'label'     => 'Content Text Color',
                        'description' => __( 'The main text color for your content.', 'wpcsc1208' ),
                    );
                }

                if(in_array ('link-color', $this->bs_csc_colors)) {
                    $colors[] = array(
                        'slug'      =>'wpcsc1208_customizer_settings[wpcsc_bootstrap_customizer][link-color]', 
                        'default'   => '#337ab7',
                        'label'     => 'Content Link Color',
                        'description' => __( 'The text color for all your links.  This is typically the same color as Brand Primary.', 'wpcsc1208' ),
                    );
                }

                if(in_array ('brand-primary', $this->bs_csc_colors)) {
                    $colors[] = array(
                        'slug'      =>'wpcsc1208_customizer_settings[wpcsc_bootstrap_customizer][brand-primary]', 
                        'default'   => '#337ab7',
                        'label'     => 'Brand Primary Color',
                        'description' => __( 'Primary color for any buttons, labels, and headings you may have.', 'wpcsc1208' ),
                    );
                }

                if(in_array ('brand-success', $this->bs_csc_colors)) {
                    $colors[] = array(
                        'slug'      =>'wpcsc1208_customizer_settings[wpcsc_bootstrap_customizer][brand-success]', 
                        'default'   => '#5cb85c',
                        'label'     => 'Success Color',
                        'description' => __( 'The color for any success labels, buttons or alerts.', 'wpcsc1208' ),
                    );
                }

                if(in_array ('brand-info', $this->bs_csc_colors)) {
                    $colors[] = array(
                        'slug'      =>'wpcsc1208_customizer_settings[wpcsc_bootstrap_customizer][brand-info]', 
                        'default'   => '#46b8da',
                        'label'     => 'Info Button Color',
                        'description' => __( 'The color for any info labels, buttons or alerts.', 'wpcsc1208' ),
                    );
                }

                if(in_array ('brand-warning', $this->bs_csc_colors)) {
                    $colors[] = array(
                        'slug'      =>'wpcsc1208_customizer_settings[wpcsc_bootstrap_customizer][brand-warning]', 
                        'default'   => '#f0ad4e',
                        'label'     => 'Warning Button Color',
                        'description' => __( 'The color for any warning labels, buttons or alerts.', 'wpcsc1208' ),
                    );
                }

                if(in_array ('brand-danger', $this->bs_csc_colors)) {
                    $colors[] = array(
                        'slug'      =>'wpcsc1208_customizer_settings[wpcsc_bootstrap_customizer][brand-danger]', 
                        'default'   => '#d9534f',
                        'label'     => 'Danger Button Color',
                        'description' => __( 'The color for any danger labels, buttons or alerts.', 'wpcsc1208' ),
                    );
                }

                foreach( $colors as $color ) {
                    // SETTINGS
                    $wp_customize->add_setting(
                        $color['slug'], array(
                            'default' => $color['default'],
                            'type' => 'option', 
                            'capability' => 'edit_theme_options',
                            'transport' => 'postMessage',
                            'sanitize_callback' => array($this, 'check_compile_bootstrap_color')
                        )
                    );
                    // CONTROLS
                    $wp_customize->add_control(
                        new WP_Customize_Color_Control(
                            $wp_customize,
                            $color['slug'], 
                            array(
                                'label' => $color['label'], 
                                'section' => 'bootstrap_colors_section',
                                'settings' => $color['slug'],
                                'description' => $color['description']
                            )
                        )
                    );
                }
            }
            
            if(!empty($this->bs_csc_fonts)) {
                $wp_customize->add_section( 'bootstrap_fonts_section', array(
                        'priority' => 10,
                        'capability' => 'edit_theme_options',
                        'theme_supports' => '',
                        'title' => __( 'Bootstrap Fonts', 'wpcsc1208' ),
                        'description' => '',
                        'panel' => 'wpcsc1208_bootstrap_options_panel',
                    )
                );

                $fonts = array();
                
                if(in_array('font-size-base', $this->bs_csc_fonts)) {
                    $fonts[] = array(
                        'slug'      =>'wpcsc1208_customizer_settings[wpcsc_bootstrap_customizer][font-size-base]', 
                        'default'   => '14px',
                        'label'     => 'Base font size',
                        'description' => __( 'The base font size for the site.', 'wpcsc1208' ),
                    );
                }
                
                foreach( $fonts as $font ) {
                    // SETTINGS
                    $wp_customize->add_setting(
                        $font['slug'], array(
                            'default' => $font['default'],
                            'type' => 'option', 
                            'capability' => 'edit_theme_options',
                            'transport' => 'postMessage',
                            'sanitize_callback' => array($this, 'check_compile_bootstrap_fonts')
                        )
                    );
                    // CONTROLS
                    $wp_customize->add_control(
                        $font['slug'], array(
                            'label' => $font['label'], 
                            'section' => 'bootstrap_fonts_section',
                            'settings' => $font['slug'],
                            'description' => $font['description']
                        )
                    );
                }
            }
            
        }
    }

    public function check_compile_bootstrap_color($color){
        // Make sure we sanatize this using default WordPress Sanatize functions
        $color = sanitize_hex_color($color);

        if(!empty($color)) {
           add_action('customize_save_after', array( $this, 'csc_bootstrap_compiler_options'));
        }

        return $color;
    }

    public function check_compile_bootstrap_fonts($fonts){
        // Make sure we sanatize this using default WordPress Sanatize functions
        //$fonts = sanitize_hex_color($fonts);

        if(!empty($fonts)) {
           add_action('customize_save_after', array( $this, 'csc_bootstrap_compiler_options'));
        }

        return $fonts;
    }
    
}

add_action( 'init' , 'csc_customizer_bootstrap_init' );

function csc_customizer_bootstrap_init() {
    $csc_customizer_bootstrap_options = new WpCscBootstrapCustomizerOptions();
    
    // Don't enqueue on admin pages
    if(!is_admin()) {
        wp_register_script('csc_bootstrapjs', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js', array('jquery'),'3.3.5', true );
        wp_register_style('csc_bootstrapcss', WPCSC_PLUGIN_URL.'/assets/bootstrap/stylesheets/bootstrap.min.css',false,'3.3.6','all');    

        wp_enqueue_script('csc_bootstrapjs');
        wp_enqueue_style('csc_bootstrapcss');
    }
}

?>