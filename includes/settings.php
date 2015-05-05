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
 * @since 1.0
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