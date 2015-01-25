<?php
/**
 * Stringcan Theme functions and definitions
 * author: ethan@getstringcan.com
 *
 * Set up the theme and provides some helper functions.Others are attached 
 * to action and filter hooks in WordPress to change core functionality.
 *
 * For more information on hooks, actions, and filters,
 * @link http://codex.wordpress.org/Plugin_API
 *
 * @since Stringcan Theme 1.3
 */

/**
 * @since version 1.1 
 *  
 * Set the parent theme for the Stringcan Theme
 *  
 * Function only called on intial theme activation if 
 * get_option( 'stringcan_theme_settings' ) is empty or doesn't exist.
 */
function stringcan_parent_theme_init($parentTheme) {

    //get stylesheet
    $stylesheet = get_stylesheet_directory() . '/style.css';

    //create an array from lines
    $lines = file($stylesheet);

    //put together a few key varables
    $template = 'Template: ' . $parentTheme . "\n";
    $textDomain = 'Text Domain: ' . $parentTheme . "\n";        
   
    //build array of keys and NEW values
    $stylesheet_output_params = array(
        $lines[9] => $template,
        $lines[10] => $textDomain
    );

    $writeOutput = str_replace(array_keys($stylesheet_output_params), array_values($stylesheet_output_params), $lines);

    //write the contents back to the file
    file_put_contents($stylesheet, $writeOutput);

    //update template with NEW parent theme
    update_option( 'template', $parentTheme );

}
add_action( 'stringcan_parent_init', 'stringcan_parent_theme_init' );

/**
 * @since version 1.1
 *
 * On theme activativation, add initial settings. Set parent to 
 * first key in the wp_get_themes() array by default. 
 *
 */
//if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
function stringcan_initialize_parent() {
    if( get_option( 'stringcan_theme_settings' ) == '' ) {

        //get array of available themes
        $themes = wp_get_themes();

        //grab the first one in the array
        $parentThemeInit = array_keys($themes)[0];

        //setup init variable
        $initStringcanSettings = array(
            'strincan_parent_theme' => $parentThemeInit,
            'stringcan_css_box' => ''
        );
        //update child theme settings
        update_option( 'stringcan_theme_settings', $initStringcanSettings );

        //update template field in database with NEW parent
        update_option('template', $parentThemeInit);

            /** 
             * @since version 1.2 - set wordpress defaults
             * 
             * update permalink structure
             * set ping status (closed)
             * set discussion status (closed)
             * set comment_registration to '1' (Users must be registered and logged in to comment)
             * set comment_moderation to '1' (Comment approval must be manual)
             */
             update_option('permalink_structure', '/%postname%/'); //update permalink to /post-name
             update_option('default_ping_status', 'closed'); //update default ping status to closed
             update_option('default_comment_status', 'closed');//update default discussion status to closed
             update_option('comment_registration', '1'); //Users must be registered and logged in to comment
             update_option('comment_moderation', '1'); //Comment must be manually approved


        //do action to update parent theme
        do_action('stringcan_parent_init', $parentThemeInit);


    }
}
add_action('after_setup_theme', 'stringcan_initialize_parent' );

/**
 * @since version 1.0
 *
 * Deregister any scripts or css here.
 *
 * Priority is key, removal should take place later down the line,
 * in this case 100. <- Keep a log of priority levels.
 *
 * Example:
 * function stringcan_remove_scripts() {}
 * add_action( 'wp_enqueue_scripts', 'remove_scripts', 100 );
 */

/**
 * @since version 1.3
 *
 * Include automatic updates from GIT repository
 */
require_once get_stylesheet_directory() . '/includes/endifMediaGitHubPluginUploader.php';

if ( is_admin() ) {
    new endifMediaGitHubPluginUpdater( __FILE__, 'endifmedia', 'stringcan-child-theme', 'a3ffced37855f6b1a98d2ee391b54519699c84be' );
}

/**
 * @since version 1.0
 *
 * Include the TGM_Plugin_Activation class.
 */
include_once get_stylesheet_directory() . '/includes/class-tgm-plugin-activation.php';

/**
 * @since version 1.0
 *
 * Register the required plugins for Stringcan Theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function stringcan_register_required_plugins() {
    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        // This is an example of how to include a plugin pre-packaged with a theme.
        array(
            'name'               => 'BackupBuddy', // The plugin name.
            'slug'               => 'backupbuddy', // The plugin slug (typically the folder name).
            'source'             => get_stylesheet_directory_uri() . '/lib/plugins/backupbuddy.zip', // The plugin source.
            'required'           => false, // If false, the plugin is only 'recommended' instead of required.
            'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url'       => '', // If set, overrides default API URL and points to an external URL.
        ),
       
        array(
            'name'      => 'Advanced Custom Fields',
            'slug'      => 'advanced-custom-fields',
            'required'  => false,
        ),

        array(
            'name'      => 'Google XML Sitemaps',
            'slug'      => 'google-xml-sitemaps',
            'required'  => false,
        ),

        array(
            'name'      => 'Crazyegg-Heatmap-Tracking',
            'slug'      => 'crazyegg-heatmap-tracking',
            'required'  => false,
        ),

        array(
            'name'      => 'GreenRope Analytics',
            'slug'      => 'greenrope-analytics',
            'required'  => false,
        ),

        array(
            'name'      => 'TinyMCE Advanced',
            'slug'      => 'tinymce-advanced',
            'required'  => false,
        ),
     
        array(
            'name'      => 'WP Smush.it',
            'slug'      => 'wp-smushit',
            'required'  => false,
        ),

    );

    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'default_path' => '',                      // Default absolute path to pre-packaged plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => __( 'Install Required Plugins', 'tgmpa' ),
            'menu_title'                      => __( 'Install Plugins', 'tgmpa' ),
            'installing'                      => __( 'Installing Plugin: %s', 'tgmpa' ), // %s = plugin name.
            'oops'                            => __( 'Something went wrong with the plugin API.', 'tgmpa' ),
            'notice_can_install_required'     => _n_noop( 'Stringcan Theme requires the following plugin: %1$s.', 'stringcan Theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'Stringcan Theme recommends the following plugin: %1$s.', 'Stringcan Theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with Stringcan Ultimate Child Theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with Stringcan Ultimate Child Theme: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
            'return'                          => __( 'Return to Required Plugins Installer', 'tgmpa' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'tgmpa' ),
            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'tgmpa' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );
    tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'stringcan_register_required_plugins' );

/** 
 * @since version 1.0
 *
 * Check Posts by Author
 *
 * Pass the author user name into the function to check if posts exist under a certain authorname.
 *  
 * Use:
 * call function and pass in author username -> author_has_posts('eallen');
 */
function author_has_posts( $author ) {
  $args = array(
    'author_name' => $author
  );
  $query = new WP_Query($args);
  if($query->have_posts()) {
    return true;
  }
  //wp_reset_postdata();
  return false;
}

/** 
 * @since version 1.0  
 *
 * Get the title and shorten it to just the first name (word)
 *
 * Call to function <?php the_title_shorter(); ?>
 * Function is used in single-staff-member.php
 *
 * This function only works if there are spaces in page title. Use another
 * function to retrieve the information.
 */
function the_title_shorter($deLimitEr = ' ', $title) {
    $title = get_the_title();
    $p = explode($deLimitEr, $title);
    
    return $p[0];
}

/**
 * @since version 1.0
 * 
 * Check the post type of slug passed.
 *
 * Call to function <?php is_post_type('type_keyword'); ?>
 * Function is a template function.
 */
function is_post_type($type){
    global $wp_query;
    if($type == get_post_type($wp_query->post->ID)) {

        return true;

    } else {

        return false;
    }
}

/**
 * @since version 1.0
 * 
 * Enqueue admin scripts
 */
function stringcan_admin_scripts() {
    wp_enqueue_script( 'ace_code_highlighter_js', get_stylesheet_directory_uri() . '/ace/ace.js', '', '1.0.0', true );
    wp_enqueue_script( 'ace_mode_js', get_stylesheet_directory_uri() . '/ace/mode-css.js', array( 'ace_code_highlighter_js' ), '1.0.0', true );
    wp_enqueue_script( 'custom_css_js', get_stylesheet_directory_uri() . '/js/custom-css.js', array( 'jquery' ), '1.0.0', true );


 
        //add back dequeued scripts here

}
add_action( 'admin_enqueue_scripts', 'stringcan_admin_scripts' );


/**
 * @since version 1.0
 * 
 * Enqueue front end scripts
 */
function stringcan_add_scripts() {
    //add parent stylesheet
    wp_enqueue_style( 'parent-theme',  get_template_directory_uri() . '/style.css' );

    //add child themes custom css
    wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . '/css/stringcan-style.css' );



        //add back dequeued scripts here

}
add_action( 'wp_enqueue_scripts', 'stringcan_add_scripts' );

/**
 * @since version 1.1 added update_option( 'template', With New Parent Theme' );
 *  
 * Set the parent theme for the Stringcan Theme
 *  
 * Function hooks into stringcan_update_parent_theme called
 * in the options_page function ONLY when a user edits the
 * general tab.
 */
function stringcan_parent_theme_setter($parentTheme) {

        //get stylesheet
        $stylesheet = get_stylesheet_directory() . '/style.css';

        //create an array from lines
        $lines = file($stylesheet);

        //put together a few key varables
        $parent = get_option('stringcan_theme_settings');

        $template = 'Template: ' . $parent['stringcan_parent_theme'] . "\n";
        $textDomain = 'Text Domain: ' . $parent['stringcan_parent_theme'] . "\n";

        //update template with NEW parent theme
        update_option( 'template', $parent['stringcan_parent_theme'] );

        //build array of keys and NEW values
        $stylesheet_output_params = array(
            $lines[9] => $template,
            $lines[10] => $textDomain
        );

        $writeOutput = str_replace(array_keys($stylesheet_output_params), array_values($stylesheet_output_params), $lines);

        //write the contents back to the file
        file_put_contents($stylesheet, $writeOutput);

}
add_action( 'stringcan_update_parent_theme', 'stringcan_parent_theme_setter' );

/** 
 * @since version 1.1
 *
 * Write minified css to style file.
 *  
 * Function hooks into stringcan_write_to_stylesheet called
 * in the options_page function ONLY when a user edits the
 * style tab.
 */
function stringcan_create_stylesheet($css) {

    //get stylesheet
    $styleFile = get_stylesheet_directory() . '/css/stringcan-style.css';

    //MINIFY!
    $css = preg_replace('/\s+/', '', $css);

    //write the contents to the file
    file_put_contents($styleFile, $css);

}
add_action( 'stringcan_write_to_stylesheet', 'stringcan_create_stylesheet' );

/**
 * @since version 1.0
 *  
 * Register options page
 */
function stringcan_create_menu() {
    //create new top-level menu
    add_menu_page(__('Theme Settings', 'stringcan'), __('Stringcan Theme', 'stringcan'), 'administrator', 'stringcan-theme-settings', 'stringcan_options_page', 'dashicons-megaphone', 52.95);   
}
add_action('admin_menu', 'stringcan_create_menu');

/**
 * Generate options page 
 *
 * @since 1.1 added tab logic to deal with default tab
 */
function stringcan_options_page(){

    //add support for decimal numbers 
    if (!current_user_can('manage_options')) {
      wp_die( _e('You do not have sufficient permissions to access this page.', 'stringcan') );
    }

    //see if the user has posted us some information
    if( isset($_POST['submit']) ){

        check_admin_referer( 'stringcan_save_form', 'stringcan_name_of_nonce' );

        //get current optons first
        $stringcanSettings = get_option( 'stringcan_theme_settings' );

            //figure out which tab we are editing - added in version 1.1
            $tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';

            switch ( $tab ) {
                case 'general' :

                    $stringcanSettings['stringcan_parent_theme'] = $_POST['stringcan-parent-theme'];
                    
                    //update options
                    update_option( 'stringcan_theme_settings', $stringcanSettings );

                    //do action to update parent theme
                    do_action('stringcan_update_parent_theme');
                break;
                case 'styles' :

                    $stringcanSettings['stringcan_css_box'] = $_POST['stringcan-css-box'];

                    //update options
                    update_option( 'stringcan_theme_settings', $stringcanSettings );

                    //do action to write stylesheet file
                    do_action('stringcan_write_to_stylesheet', $stringcanSettings['stringcan_css_box']);
                break;
            }
         
            $stringcan_success = true; //issue success variable 


?>

<?php if(isset($animation_em_fail)) { ?>
<div class="error">
    <p><strong><?php _e("$stringcan_fail", 'stringcan' ); ?></strong></p>
</div>
<?php } ?>

<?php if(isset($stringcan_success) && $stringcan_success == true) { ?>
<div class="updated">
    <p><strong><?php _e('settings saved.', 'stringcan' ); ?></strong></p>
</div>
<?php } ?>

<?php }

    echo '<div class="wrap">';

    echo "<h2>" . __( 'Stringcan Theme Options', 'stringcan' ) . "</h2><br>
          <p>" . __( '', 'stringcan') ."</p>";

?>
        <?php
            
            //figure out which tab was clicked
            $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';

        ?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=stringcan-theme-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
            <a href="?page=stringcan-theme-settings&tab=styles" class="nav-tab <?php echo $active_tab == 'styles' ? 'nav-tab-active' : ''; ?>">Styles</a>
        </h2>

        <form id="stringcan-login-form" method="post" action="">
          <?php wp_nonce_field( 'stringcan_save_form', 'stringcan_name_of_nonce' ); ?>

            <?php if( $active_tab == 'general' ) { 

                $stringcanSettings = get_option( 'stringcan_theme_settings' );

             ?>
                <table class="form-table"> 
                    <tr valign="middle">
                        <th scope="row">
                            <label for="animation"><?php _e( 'Parent Theme', 'stringcan' ); ?></label>
                        </th>
                        <td>                   
                            <select name="stringcan-parent-theme">
                                <?php 

                                    $themes = wp_get_themes(); 

                                    foreach($themes as $theme) { 

                                    //check if theme is a child
                                    $is_child = $theme->parent();

                                    //filter out Stringcan theme
                                    if( empty($is_child) ) {
                                     echo '<option value="' . $theme->template . '" ' . selected($stringcanSettings['stringcan_parent_theme'], $theme->template ) . '>' . $theme->Name . '</option>';
                                    }

                                }
                                ?>
                            </select> 
                        </td>
                    </tr> 
                </table>
            <?php } 
                elseif( $active_tab == 'styles' ) {           
                $stringcanSettings = get_option( 'stringcan_theme_settings' );
            ?>
                <table class="form-table">
                    <tr>
                        <td>
                            <th scope="row"></th>

                            <div id="custom_css_container">
                                <div name="custom_css" id="custom_css" style="border: 1px solid #DFDFDF; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; width: 758px; height: 400px; position: relative; margin-top: 3em;"></div>
                            </div>

                            <textarea name="stringcan-css-box" id="stringcan-css-box" style="display: none;"><?php print esc_textarea( $stringcanSettings['stringcan_css_box'] ); ?></textarea>
                        </td>
                    </tr>
                </table> 
            <?php } ?>
          <p class="submit">
           <input type="submit" class="button-primary" name="submit" value="<?php _e('Save Changes') ?>" />
          </p>
          <!--<div class="alert alert-warning">
            <strong><?php _e( 'Hey there!', 'stringcan' ); ?></strong><?php _e( ' If you enjoy this plugin, please ', 'stringcan' ); ?><a href="http://wordpress.org/plugins/animated-login/"><?php _e( 'rate it!', 'stringcan' ) ;?></a>
          </div>-->
        </form>
    </div>
<?php } // function animated_em_options_page() closed 
