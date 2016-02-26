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
        $this->options = get_option( 'wpcsc1208_option_settings' );
        ?>
        <div class="wrap">
            <h2>Customizer Sass Compiler Settings</h2>           
            <p>
                <span class="version">Version: <em><?= $this->options['wpcscs_version']; ?></em>
                <br/>
                <span class="author">By: <a href="http://michaelcread.com" target="_blank">Michael Read</a></span>
                <br/>
                <span class="repo">Help &amp; Issues: <a href="https://github.com/mread1208/wordpress-customizer-sass-compiler" target="_blank">Github</a></span>
            </p>
            <form method="post" action="options.php">
                <?php // This prints out all hidden setting fields
                    settings_fields('wpcsc1208_option_settings_group');
                    do_settings_sections('csc-plugin-settings');
                ?>
                <?php // So we don't overwrite our version number in the DB ?>
                <input type="hidden" name="wpcsc1208_option_settings[wpcscs_version]" value="<?= WPCSC_VERSION_NUM; ?>" />
                <?php // We put the CSS content back in when we sanitize, keys are in there for looping in the sanitize function 
                foreach($this->options['wpcsc_content'] as $key => $value) { ?>
                    <input type="hidden" name="wpcsc1208_option_settings[wpcsc_content][<?= $key; ?>]" value="" />
                <?php } ?>
                
                <?php submit_button();  ?>
            </form>
            <?php
            
            //echo '<p>wpcsc1208_option_settings</p> <pre>';
            //print_r(get_option('wpcsc1208_option_settings'));
            //echo '</pre><p>wpcsc1208_customizer_settings</p> <pre>';
            //print_r(get_option('wpcsc1208_customizer_settings'));
            //echo '</pre>'; 
            
            ?>
            
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'wpcsc1208_option_settings_group', // Option group
            'wpcsc1208_option_settings', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'wpcsc1208_styles_include_id', // ID
            'Default Stylesheets / JS / and SASS files to Include', // Title
            array( $this, 'csc_styles_includes_info' ), // Callback
            'csc-plugin-settings' // Page
        );  

        add_settings_field(
            'bootstrap', // ID
            'Include Bootstrap', // Title 
            array( $this, 'bootstrap_callback' ), // Callback
            'csc-plugin-settings', // Page
            'wpcsc1208_styles_include_id' // Section           
        );
        
        add_settings_field(
            'custom', // ID
            'Include Custom Options', // Title 
            array( $this, 'custom_callback' ), // Callback
            'csc-plugin-settings', // Page
            'wpcsc1208_styles_include_id' // Section           
        );
        
        $this->options = get_option('wpcsc1208_option_settings');
        
        if(isset($this->options['wpcsc_styles_include']['bootstrap']) && $this->options['wpcsc_styles_include']['bootstrap']) {
            
            add_settings_section(
                'csc_bootstrap_options_id', // ID
                'Bootstrap SASS Variables to include', // Title
                array( $this, 'csc_bootstrap_options_info' ), // Callback
                'csc-plugin-settings' // Page
            );  

            add_settings_field(
                'wpcsc_bootstrap_options', // ID
                'Bootstrap Variables to include', // Title 
                array( $this, 'bootstrap_options_callback' ), // Callback
                'csc-plugin-settings', // Page
                'csc_bootstrap_options_id' // Section           
            );
        }
        
        if(isset($this->options['wpcsc_styles_include']['custom']) && $this->options['wpcsc_styles_include']['custom']) {
        
            add_settings_section(
                'csc_custom_options_id', // ID
                'Custom SASS file and Variables', // Title
                array( $this, 'csc_custom_options_info' ), // Callback
                'csc-plugin-settings' // Page
            );  

            add_settings_field(
                'wpcsc_custom_sass_file', // ID
                'Path to Custom Sass File (exclude file name)', // Title 
                array( $this, 'custom_path_callback' ), // Callback
                'csc-plugin-settings', // Page
                'csc_custom_options_id' // Section           
            );
            
            add_settings_field(
                'wpcsc_custom_sass_file_name', // ID
                'Name of Custom Sass File', // Title 
                array( $this, 'custom_name_callback' ), // Callback
                'csc-plugin-settings', // Page
                'csc_custom_options_id' // Section           
            );
            
            add_settings_field(
                'wpcsc_custom_css_file_path_name', // ID
                'Path and Name of Custom CSS File (ex. /assets/mystyles.css)', // Title 
                array( $this, 'custom_css_path_name_callback' ), // Callback
                'csc-plugin-settings', // Page
                'csc_custom_options_id' // Section           
            );
            
            add_settings_field(
                'wpcsc_custom_options', // ID
                'Custom Variables to include', // Title 
                array( $this, 'custom_options_callback' ), // Callback
                'csc-plugin-settings', // Page
                'csc_custom_options_id' // Section           
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
        if( isset( $input['wpcsc_styles_include']['bootstrap'] ) )
            $new_input['wpcsc_styles_include']['bootstrap'] = absint( $input['wpcsc_styles_include']['bootstrap'] );
        
        if( isset( $input['wpcsc_styles_include']['custom'] ) )
            $new_input['wpcsc_styles_include']['custom'] = absint( $input['wpcsc_styles_include']['custom'] );
        
        if( isset( $input['wpcscs_version'] ) )
            $new_input['wpcscs_version'] = sanitize_text_field( $input['wpcscs_version'] );
        
        if( isset( $input['wpcsc_content'] ) ) {
            foreach($input['wpcsc_content'] as $key => $value) {
                if($value == '') {
                    $this->options = get_option( 'wpcsc1208_option_settings' );
                    $new_input['wpcsc_content'][$key] = wp_kses( $this->options['wpcsc_content'][$key], array( '\'', '\"' ) ); 
                } else {
                    $new_input['wpcsc_content'][$key] = wp_kses( $value, array( '\'', '\"' ) ); 
                }
            }
        }
            
        
        if( isset( $input['wpcsc_bootstrap_options']['color_variables'] ) ) {
            foreach($input['wpcsc_bootstrap_options']['color_variables'] as $bs_color_option) {
                $new_input['wpcsc_bootstrap_options']['color_variables'][] = sanitize_text_field($bs_color_option);
            }
        }
        
        if( isset( $input['wpcsc_bootstrap_options']['font_variables'] ) ) {
            foreach($input['wpcsc_bootstrap_options']['font_variables'] as $bs_font_option) {
                $new_input['wpcsc_bootstrap_options']['font_variables'][] = sanitize_text_field($bs_font_option);
            }
        }
        
        // Custom SASS options
        
        if( isset( $input['wpcsc_custom_options']['custom_sass_path'] ) )
            $new_input['wpcsc_custom_options']['custom_sass_path'] = sanitize_text_field( $input['wpcsc_custom_options']['custom_sass_path'] );
        
        if( isset( $input['wpcsc_custom_options']['custom_sass_name'] ) )
            $new_input['wpcsc_custom_options']['custom_sass_name'] = sanitize_text_field( $input['wpcsc_custom_options']['custom_sass_name'] );
        
        if( isset( $input['wpcsc_custom_options']['custom_css_path_name'] ) )
            $new_input['wpcsc_custom_options']['custom_css_path_name'] = sanitize_text_field( $input['wpcsc_custom_options']['custom_css_path_name'] );
        
        if( isset( $input['wpcsc_custom_options']['custom_sass_variables'] ) ) {
            $i = 0;
            foreach($input['wpcsc_custom_options']['custom_sass_variables'] as $custom_sass_var) {
                $new_input['wpcsc_custom_options']['custom_sass_variables'][$i]['key'] = sanitize_text_field($custom_sass_var['key']);
                $new_input['wpcsc_custom_options']['custom_sass_variables'][$i]['value'] = sanitize_text_field($custom_sass_var['value']);
                $i++;
            }
        }
        
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function csc_styles_includes_info() {
        print 'Choose which libraries you would like to include.';
    }
    
    public function csc_bootstrap_options_info() {
        print 'Choose which Bootstrap variables you would like to include / exclude. These options will appear in your theme customizer under the "Bootstrap Options" panel';
    }
    
    public function csc_custom_options_info() {
        print 'Add the path to your custom SASS file.  Paths should start with the root of your theme. example: "/library/scss/".  After you add your path, you may add your own variables below.  Adding these variables will allow you to modify your default SASS options in the theme customizer.';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    
    // Library Includes
    public function bootstrap_callback() { 
        $this->options = get_option('wpcsc1208_option_settings');
        $html = '<input type="radio" name="wpcsc1208_option_settings[wpcsc_styles_include][bootstrap]" '.checked(isset($this->options['wpcsc_styles_include']['bootstrap']) ? $this->options['wpcsc_styles_include']['bootstrap'] : '', 1, false).' value="1" /> Yes ';
        $html .= '<input type="radio" name="wpcsc1208_option_settings[wpcsc_styles_include][bootstrap]"'.checked(isset($this->options['wpcsc_styles_include']['bootstrap']) ? $this->options['wpcsc_styles_include']['bootstrap'] : '', 0, false).' value="0" /> No ';
        echo $html;
    }
    
    public function custom_callback() {
        $this->options = get_option('wpcsc1208_option_settings');
        $html = '<input type="radio" name="wpcsc1208_option_settings[wpcsc_styles_include][custom]" '.checked(isset($this->options['wpcsc_styles_include']['custom']) ? $this->options['wpcsc_styles_include']['custom'] : '', 1, false).' value="1" /> Yes ';
        $html .= '<input type="radio" name="wpcsc1208_option_settings[wpcsc_styles_include][custom]"'.checked(isset($this->options['wpcsc_styles_include']['custom']) ? $this->options['wpcsc_styles_include']['custom'] : '', 0, false).' value="0" /> No ';
        echo $html;
    }
    
    //Library Options
    public function bootstrap_options_callback() { 
        $this->options = get_option('wpcsc1208_option_settings');
        $default_bs_colors = array('body-bg', 'text-color', 'link-color', 'brand-primary', 'brand-success', 'brand-info', 'brand-warning', 'brand-danger');
        $default_bs_fonts = array('font-size-base');
        
        $html = '';
        
        // Colors
        foreach($default_bs_colors as $default_bs_color) {
            $html .= '<input type="checkbox" name="wpcsc1208_option_settings[wpcsc_bootstrap_options][color_variables][]" '.checked( $default_bs_color, isset($this->options['wpcsc_bootstrap_options']['color_variables']) && in_array($default_bs_color, $this->options['wpcsc_bootstrap_options']['color_variables']) ? $default_bs_color : '', false ).' value="'.$default_bs_color.'" /> '.$default_bs_color.'<br />';
        }
        
        // Fonts
        foreach($default_bs_fonts as $default_bs_font) {
            $html .= '<input type="checkbox" name="wpcsc1208_option_settings[wpcsc_bootstrap_options][font_variables][]" '.checked( $default_bs_font, isset($this->options['wpcsc_bootstrap_options']['font_variables']) && in_array($default_bs_font, $this->options['wpcsc_bootstrap_options']['font_variables']) ? $default_bs_font : '', false ).' value="'.$default_bs_font.'" /> '.$default_bs_font.'<br />';
        }
        
        echo $html;
    }
    
    public function custom_path_callback() { 
        $this->options = get_option('wpcsc1208_option_settings');
        $value = isset($this->options['wpcsc_custom_options']['custom_sass_path']) ? $this->options['wpcsc_custom_options']['custom_sass_path'] : '';
        $html = '<input type="text" name="wpcsc1208_option_settings[wpcsc_custom_options][custom_sass_path]" value="'.$value.'" placeholder="ex. /assets/scss/" required="required" class="wpcsc-js-whitespace-validate" />';
        echo $html;
    }
    
    public function custom_name_callback() { 
        $this->options = get_option('wpcsc1208_option_settings');
        $value = isset($this->options['wpcsc_custom_options']['custom_sass_name']) ? $this->options['wpcsc_custom_options']['custom_sass_name'] : '';
        $html = '<input type="text" name="wpcsc1208_option_settings[wpcsc_custom_options][custom_sass_name]" value="'.$value.'" placeholder="ex. style.scss" required="required" class="wpcsc-js-whitespace-validate" />';
        echo $html;
    }
    
    public function custom_css_path_name_callback(){
        $this->options = get_option('wpcsc1208_option_settings');
        $value = isset($this->options['wpcsc_custom_options']['custom_css_path_name']) ? $this->options['wpcsc_custom_options']['custom_css_path_name'] : '';
        $html = '<input type="text" name="wpcsc1208_option_settings[wpcsc_custom_options][custom_css_path_name]" value="'.$value.'" placeholder="ex. style.scss" required="required" class="wpcsc-js-whitespace-validate" />';
        echo $html;
    }
    
    public function custom_options_callback() { 
        $this->options = get_option('wpcsc1208_option_settings');
        
        $html = '<div class="wpcsc-multifield-wrapper">
            <table class="form-table wpcsc-multifields" style="max-width: 300px">
                <thead><tr><th>Variable Name</th><th>Default Value</th><th>&nbsp;</th></thead>
                <tfoot><tr><td colspan="3"><a href="#" class="button wpcsc-js-add-repeater-field">Add New Sass Variable</a></td></tr></tfoot>
                <tbody>';
        if(!empty($this->options['wpcsc_custom_options'])){
            for($i = 0; $i < count($this->options['wpcsc_custom_options']['custom_sass_variables']); ++$i) {
                $html .= '<tr class="wpcsc-multi-field">
                    <td><input type="text" name="wpcsc1208_option_settings[wpcsc_custom_options][custom_sass_variables]['.$i.'][key]" value="'.$this->options['wpcsc_custom_options']['custom_sass_variables'][$i]['key'].'" placeholder="Sass Variable" required="required" class="wpcsc-js-whitespace-validate" /></td>
                    <td><input type="text" name="wpcsc1208_option_settings[wpcsc_custom_options][custom_sass_variables]['.$i.'][value]" value="'.$this->options['wpcsc_custom_options']['custom_sass_variables'][$i]['value'].'" placeholder="Default Value" required="required" class="wpcsc-js-whitespace-validate" /></td>
                    <td><a href="#" class="button wpcsc-js-remove-repeater-field">Remove</a>
                </tr>';
            }
        }
        $html .= '</tbody></table></div>';
        echo $html;
        
    }
    
}