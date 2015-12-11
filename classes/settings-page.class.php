<?php
class CspSettingsPage
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
            'csp-plugin-settings', 
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
        $this->options = get_option( 'csp_styles_include' );
        ?>
        <div class="wrap">
            <h2>Customizer Sass Compiler Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'csp_styles_include_group' );   
                do_settings_sections( 'csp-plugin-settings' );
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
            'csp_styles_include_group', // Option group
            'csp_styles_include', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'csp_styles_include_id', // ID
            'Default Stylesheets / JS / and SASS files to Include', // Title
            array( $this, 'print_section_info' ), // Callback
            'csp-plugin-settings' // Page
        );  

        add_settings_field(
            'bootstrap', // ID
            'Include Bootstrap', // Title 
            array( $this, 'bootstrap_callback' ), // Callback
            'csp-plugin-settings', // Page
            'csp_styles_include_id' // Section           
        );    
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

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        //print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function bootstrap_callback()
    { ?>
        <input type="radio" id="bootstrap" name="csp_styles_include[bootstrap]"<?= ($this->options['bootstrap'] == '1') ? 'checked="checked"' : ''; ?> value="1" />Yes
        <input type="radio" id="bootstrap" name="csp_styles_include[bootstrap]"<?= ($this->options['bootstrap'] == '0' || $this->options['bootstrap'] == '') ? 'checked="checked"' : ''; ?> value="0" />No
    <?php }
    
}

if(is_admin()) {
    $csp_settings_page = new CspSettingsPage();
}