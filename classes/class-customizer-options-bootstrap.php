<?php

class WpCscBootstrapCustomizerOptions extends WpCscCustomizerOptions
{
    
    public function __construct() {
        add_action('customize_register', array($this, 'csc_customize_bootstrap_register'));
    }
    
    public function csc_bootstrap_compiler_options() {
        // Get all variables from the customizer
        $csc_colors = get_option('csc_bootstrap_colors', array());
        $csc_fonts = get_option('csc_bootstrap_fonts', array());

        $sass_vars = array_merge($csc_colors, $csc_fonts);
        $sass_import_file = '@import "_bootstrap.scss";';
        $scss_dir = WPCSC_PLUGIN_DIR.'/assets/bootstrap/stylesheets/';
        $css_file = WPCSC_PLUGIN_DIR.'/assets/bootstrap/stylesheets/bootstrap.min.css';
        
        $this->run_compiler($scss_dir, $css_file, $sass_vars, $sass_import_file);
    }

    public function csc_customize_bootstrap_register($wp_customize){
        
        $wp_customize->add_panel( 'bootstrap_options_panel', array(
                'title'       => __('Bootstrap Options', 'csc'),
                'description' => __('Modify the existing bootstrap colors', 'csc'),
                'priority'    => 10,
            )
        );

        $wp_customize->add_section( 'bootstrap_colors_section', array(
                'priority' => 10,
                'capability' => 'edit_theme_options',
                'theme_supports' => '',
                'title' => __( 'Bootstrap Colors', 'csc' ),
                'description' => '',
                'panel' => 'bootstrap_options_panel',
            )
        );

        $wp_customize->add_section( 'bootstrap_fonts_section', array(
                'priority' => 10,
                'capability' => 'edit_theme_options',
                'theme_supports' => '',
                'title' => __( 'Bootstrap Fonts', 'csc' ),
                'description' => '',
                'panel' => 'bootstrap_options_panel',
            )
        );

        $colors = array();
        $colors[] = array(
            'slug'      =>'csc_bootstrap_colors[body-bg]', 
            'default'   => '#ffffff',
            'label'     => 'Background Color',
            'description' => __( 'The main body background color.', 'csc' ),
        );

        $colors[] = array(
            'slug'      =>'csc_bootstrap_colors[text-color]', 
            'default'   => '#333333',
            'label'     => 'Content Text Color',
            'description' => __( 'The main text color for your content.', 'csc' ),
        );
        $colors[] = array(
            'slug'      =>'csc_bootstrap_colors[link-color]', 
            'default'   => '#337ab7',
            'label'     => 'Content Link Color',
            'description' => __( 'The text color for all your links.  This is typically the same color as Brand Primary.', 'csc' ),
        );
        $colors[] = array(
            'slug'      =>'csc_bootstrap_colors[brand-primary]', 
            'default'   => '#337ab7',
            'label'     => 'Brand Primary Color',
            'description' => __( 'Primary color for any buttons, labels, and headings you may have.', 'csc' ),
        );
        $colors[] = array(
            'slug'      =>'csc_bootstrap_colors[brand-success]', 
            'default'   => '#5cb85c',
            'label'     => 'Success Color',
            'description' => __( 'The color for any success labels, buttons or alerts.', 'csc' ),
        );
        $colors[] = array(
            'slug'      =>'csc_bootstrap_colors[brand-info]', 
            'default'   => '#46b8da',
            'label'     => 'Info Button Color',
            'description' => __( 'The color for any info labels, buttons or alerts.', 'csc' ),
        );
        $colors[] = array(
            'slug'      =>'csc_bootstrap_colors[brand-warning]', 
            'default'   => '#f0ad4e',
            'label'     => 'Warning Button Color',
            'description' => __( 'The color for any warning labels, buttons or alerts.', 'csc' ),
        );
        $colors[] = array(
            'slug'      =>'csc_bootstrap_colors[brand-danger]', 
            'default'   => '#d9534f',
            'label'     => 'Danger Button Color',
            'description' => __( 'The color for any danger labels, buttons or alerts.', 'csc' ),
        );
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

        $fonts = array();
        $fonts[] = array(
            'slug'      =>'csc_bootstrap_fonts[font-size-base]', 
            'default'   => '14px',
            'label'     => 'Base font size',
            'description' => __( 'The base font size for the site.', 'csc' ),
        );
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