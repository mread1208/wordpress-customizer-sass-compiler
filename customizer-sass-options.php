<?php
class WpCscSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_menu_page(
            'Sass Compiler Settings', 
            'Customizer Sass Compiler Settings', 
            'manage_options', 
            'csc-plugin-settings', 
            array($this, 'create_admin_page'),
            'dashicons-art'
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'csc_styles_include' );
        ?>
        <div class="wrap">
            <h2>Customizer Sass Compiler Settings</h2>           
            <p>
                <span class="version">Version <em><?php echo get_option('wpcsc_version'); ?></em>
                <br/>
                <span class="author">By: <a href="http://michaelcread.com" target="_blank">Michael Read</a></span>
                <br/>
                <span class="repo">Help &amp; Issues: <a href="https://github.com/mread1208/wordpress-customizer-sass-compiler" target="_blank">Github</a></span>
            </p>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'csc_styles_include_group' );
                settings_fields( 'csc_bootstrap_options_group' );
                do_settings_sections( 'csc-plugin-settings' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'csc_styles_include_group', // Option group
            'csc_styles_include', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'csc_styles_include_id', // ID
            'Default Stylesheets / JS / and SASS files to Include', // Title
            array( $this, 'csc_styles_includes_info' ), // Callback
            'csc-plugin-settings' // Page
        );  

        add_settings_field(
            'bootstrap', // ID
            'Include Bootstrap', // Title 
            array( $this, 'bootstrap_callback' ), // Callback
            'csc-plugin-settings', // Page
            'csc_styles_include_id' // Section           
        );
        
        $this->options = get_option('csc_styles_include');
        
        if(isset($this->options) && $this->options['bootstrap']) {
        
            register_setting(
                'csc_bootstrap_options_group', // Option group
                'csc_bootstrap_options', // Option name
                array( $this, 'sanitize' ) // Sanitize
            );

            add_settings_section(
                'csc_bootstrap_options_id', // ID
                'Bootstrap SASS Variables to include', // Title
                array( $this, 'csc_bootstrap_options_info' ), // Callback
                'csc-plugin-settings' // Page
            );  

            add_settings_field(
                'bootstrap', // ID
                'Bootstrap Variables to include', // Title 
                array( $this, 'bootstrap_options_callback' ), // Callback
                'csc-plugin-settings', // Page
                'csc_bootstrap_options_id' // Section           
            );
        }
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['bootstrap'] ) )
            $new_input['bootstrap'] = absint( $input['bootstrap'] );
        if( isset( $input['csc_bootstrap_options'] ) )
            $new_input['csc_bootstrap_options'] = $input['csc_bootstrap_options'];
        return $input;
    }

    /** 
     * Print the Section text
     */
    public function csc_styles_includes_info() {
        print 'Choose which libraries you would like to include.';
    }
    
    public function csc_bootstrap_options_info() {
        print 'Choose which Bootstrap variables you would like to include / exclude.';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function bootstrap_callback() { 
        $this->options = get_option('csc_styles_include');
        $html = "<input type='radio' name='csc_styles_include[bootstrap]' ".checked($this->options['bootstrap'], 1, false)." value='1' /> Yes ";
        $html .= '<input type="radio" name="csc_styles_include[bootstrap]"'.checked(isset($this->options['bootstrap']) ? $this->options['bootstrap'] : '', 0, false).'value="0" /> No ';
        echo $html;
    }
    
    public function bootstrap_options_callback() { 
        $this->options = get_option('csc_bootstrap_options');
        $default_bs_colors = array('body-bg', 'text-color', 'link-color', 'brand-primary', 'brand-success', 'brand-info', 'brand-warning', 'brand-danger');
        $default_bs_fonts = array('font-size-base');
        
        $html = '';
        
        // Colors
        foreach($default_bs_colors as $default_bs_color) {
            $html .= '<input type="checkbox" name="csc_bootstrap_options[color-variables][]" '.checked( $default_bs_color, isset($this->options['color-variables']) && in_array($default_bs_color, $this->options['color-variables']) ? $default_bs_color : '', false ).' value="'.$default_bs_color.'" /> '.$default_bs_color.'<br />';
        }
        
        // Fonts
        foreach($default_bs_fonts as $default_bs_font) {
            $html .= '<input type="checkbox" name="csc_bootstrap_options[font-variables][]" '.checked( $default_bs_font, isset($this->options['font-variables']) && in_array($default_bs_font, $this->options['font-variables']) ? $default_bs_font : '', false ).' value="'.$default_bs_font.'" /> '.$default_bs_font.'<br />';
        }
        
        echo $html;
    }
    
}