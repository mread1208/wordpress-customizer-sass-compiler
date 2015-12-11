<?php

class CspBootstrapCustomizerOptions extends CspCustomizerOptions
{
    
    public function __construct() {
        add_action('customize_register', array($this, 'csp_customize_bootstrap_register'));
    }
    
    public function csp_bootstrap_compiler_options() {
        // Get all variables from the customizer
        $cspColors = get_option('csp_bootstrap_colors', array());
        $cspFonts = get_option('csp_bootstrap_fonts', array());

        $sassVariables = array_merge($cspColors, $cspFonts);
        $sassCompileFile = '@import "bootstrap.scss";';
        $cssFilePath = plugin_dir_path( __FILE__ ).'../assets/stylesheets/bootstrap.min.css';
        
        $this->run_compiler($sassVariables, $sassCompileFile, $cssFilePath);
    }

    public function csp_customize_bootstrap_register($wp_customize){
        
        $wp_customize->add_panel( 'bootstrap_options_panel', array(
                'title'       => __('Bootstrap Options', 'csp'),
                'description' => __('Modify the existing bootstrap colors', 'csp'),
                'priority'    => 10,
            )
        );

        $wp_customize->add_section( 'bootstrap_colors_section', array(
                'priority' => 10,
                'capability' => 'edit_theme_options',
                'theme_supports' => '',
                'title' => __( 'Bootstrap Colors', 'csp' ),
                'description' => '',
                'panel' => 'bootstrap_options_panel',
            )
        );

        $wp_customize->add_section( 'bootstrap_fonts_section', array(
                'priority' => 10,
                'capability' => 'edit_theme_options',
                'theme_supports' => '',
                'title' => __( 'Bootstrap Fonts', 'csp' ),
                'description' => '',
                'panel' => 'bootstrap_options_panel',
            )
        );

        $colors = array();
        $colors[] = array(
            'slug'      =>'csp_bootstrap_colors[body-bg]', 
            'default'   => '#ffffff',
            'label'     => 'Background Color',
            'description' => __( 'The main body background color.', 'csp' ),
        );

        $colors[] = array(
            'slug'      =>'csp_bootstrap_colors[text-color]', 
            'default'   => '#333333',
            'label'     => 'Content Text Color',
            'description' => __( 'The main text color for your content.', 'csp' ),
        );
        $colors[] = array(
            'slug'      =>'csp_bootstrap_colors[link-color]', 
            'default'   => '#337ab7',
            'label'     => 'Content Link Color',
            'description' => __( 'The text color for all your links.  This is typically the same color as Brand Primary.', 'csp' ),
        );
        $colors[] = array(
            'slug'      =>'csp_bootstrap_colors[brand-primary]', 
            'default'   => '#337ab7',
            'label'     => 'Brand Primary Color',
            'description' => __( 'Primary color for any buttons, labels, and headings you may have.', 'csp' ),
        );
        $colors[] = array(
            'slug'      =>'csp_bootstrap_colors[brand-success]', 
            'default'   => '#5cb85c',
            'label'     => 'Success Color',
            'description' => __( 'The color for any success labels, buttons or alerts.', 'csp' ),
        );
        $colors[] = array(
            'slug'      =>'csp_bootstrap_colors[brand-info]', 
            'default'   => '#46b8da',
            'label'     => 'Info Button Color',
            'description' => __( 'The color for any info labels, buttons or alerts.', 'csp' ),
        );
        $colors[] = array(
            'slug'      =>'csp_bootstrap_colors[brand-warning]', 
            'default'   => '#f0ad4e',
            'label'     => 'Warning Button Color',
            'description' => __( 'The color for any warning labels, buttons or alerts.', 'csp' ),
        );
        $colors[] = array(
            'slug'      =>'csp_bootstrap_colors[brand-danger]', 
            'default'   => '#d9534f',
            'label'     => 'Danger Button Color',
            'description' => __( 'The color for any danger labels, buttons or alerts.', 'csp' ),
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
            'slug'      =>'csp_bootstrap_fonts[font-size-base]', 
            'default'   => '14px',
            'label'     => 'Base font size',
            'description' => __( 'The base font size for the site.', 'csp' ),
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
           add_action('customize_save_after', array( $this, 'csp_bootstrap_compiler_options'));
        }

        return $color;
    }

    public function check_compile_bootstrap_fonts($fonts){
        // Make sure we sanatize this using default WordPress Sanatize functions
        //$fonts = sanitize_hex_color($fonts);

        if(!empty($fonts)) {
           add_action('customize_save_after', array( $this, 'csp_bootstrap_compiler_options'));
        }

        return $fonts;
    }
    
}

add_action( 'init' , 'csp_customizer_bootstrap_init' );

function csp_customizer_bootstrap_init() {
    $csp_customizer_bootstrap_options = new CspBootstrapCustomizerOptions();
    
    wp_register_script('csp_bootstrapjs', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js', array('jquery'),'3.3.5', true );
    wp_register_style('csp_bootstrapcss', plugin_dir_url( __FILE__ ).'../assets/stylesheets/bootstrap.min.css',false,'3.3.6','all');    

    wp_enqueue_script('csp_bootstrapjs');
    wp_enqueue_style('csp_bootstrapcss');
}

?>