<?php
/**
 * Facebook Album Sync Settings
 *
 * All functionality pertaining to settings.
 * - Settings menu item
 * - Settings Admin Page
 * - Settings Data
 */

//****
//
//
// Hooks and Filter calls
//
//
//***

add_action('admin_menu', 'fbas_add_settings_menu');
add_action( 'admin_init', 'fbas_settings_init' );
add_action( 'admin_enqueue_scripts', 'fbas_add_settings_js' );
//****
//
//
// Functions Called from Hooks above
//
//
//***

/**
 * Add the settings menu on the admin page
 *
 * @since 1.0
 */
function fbas_add_settings_menu(  ) {
    add_options_page('Facebook Album Sync', 'Facebook Albums', 'manage_options', 'facebook_albums_sync', 'fbas_options_page');
}

/**
 * Initialize the setting fields and options
 *
 * @since 1.0
 */
function fbas_settings_init(  ) {

    // Page name setting and Settings group
    register_setting( 'fbas_settings_group', 'fbas_page' );

    // Settings Section
    add_settings_section(
        'fbas_general_settings_section',
        __( 'General Settings', 'facebook-album-sync' ),
        'fbas_general_settings_section_callback',
        'fbas_options_page'
    );

    // Fields for general_settings_section section
    add_settings_field(
        'fbas_page',
        __( 'Enter your facebook Page Name', 'facebook-album-sync' ),
        'fbas_text_field_page_render',
        'fbas_options_page',
        'fbas_general_settings_section'
    );


}

/**
 * Render the Pane Name Setting
 * @since 1.0
 */
function fbas_text_field_page_render( ) {
    ?>
    <strong>https://facebook.com/</strong><input type='text' name='fbas_page' value='<?php echo get_option( 'fbas_page' ); ?>'>
    <img id="fbas_loading" class="hidden" src="<?php echo fbas_get_plugin_url().'images/busy.gif' ?>" >
<?php
}

/**
 * Settings Section call back
 */
function fbas_general_settings_section_callback(  ) {
    //
}

/**
 * Showing the options page
 * FBAS options page call back, registered when adding the sub menu.
 *
 * @since 0.5
 */
function fbas_options_page(  ) {

    ?>
    <form action='options.php' method='post'>

        <h2>Facebook Albums Sync Settings</h2>

        <?php
        settings_fields( 'fbas_settings_group' );
        do_settings_sections( 'fbas_options_page' );
        submit_button();
        ?>

    </form>
<?php
} // fbas_options_page

/**
 * Load the settings javascript file
 *
 * @since 0.6
 * @param $hook
 */
function fbas_add_settings_js( $hook ){
    if( 'settings_page_facebook_albums_sync' != $hook ){
        return;
    }
    wp_enqueue_script('fbas_settings_js', fbas_get_plugin_url().'js/admin/settings.js',array('jquery','underscore','backbone'), fbas_version(), true );
    wp_enqueue_style('fbas_settings_css', fbas_get_plugin_url().'css/admin/style.css');
}// end add settings