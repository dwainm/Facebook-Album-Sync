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

// hook in to get settings from ajax
add_action( 'wp_ajax_save_settings', 'ajax_save_settings' );
add_action( 'wp_ajax_no_priv_save_settings', 'ajax_save_settings' );
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

    // Settings:
    register_setting( 'fbas_settings_group', 'fbas_page' );
    register_setting( 'fbas_settings_group', 'fbas_exclude_albums');
    register_setting( 'fbas_settings_group', 'fbas_excluded_ids');

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

    add_settings_field(
        'fbas_exclude_albums',
        __( 'Would you like to exclude albums?', 'facebook-album-sync' ),
        'fbas_checkbox_field_exclude_ablums_render',
        'fbas_options_page',
        'fbas_general_settings_section'
    );

    add_settings_field(
        'fbas_excluded_ids',
        __( '', 'facebook-album-sync' ),
        'fbas_albums_area_render',
        'fbas_options_page',
        'fbas_general_settings_section'
    );


}

function get_loading_gif( $id='fbas_loading', $classes='hidden'  ){
    return  '<img id="'. $id . '" class="'.$classes.'" src="'.fbas_get_plugin_url().'images/busy.gif'.'" >';
}

/**
 * Render the Pane Name Setting
 * @since 1.0
 */
function fbas_text_field_page_render( ) {
    ?>
    <strong>https://facebook.com/</strong><input type='text' name='fbas_page' value='<?php echo get_option( 'fbas_page' ); ?>'>
    <?php echo get_loading_gif('fbas_page_name_loading'); ?>
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
    wp_enqueue_script('fbas-model-album',fbas_get_plugin_url().'js/models/album.js', array('jquery','underscore','backbone'), fbas_version() , true );
    wp_enqueue_script('fbas_settings_js', fbas_get_plugin_url().'js/admin/settings.js',array('fbas-model-album'), fbas_version(), true );
    wp_enqueue_style('fbas_settings_css', fbas_get_plugin_url().'css/admin/style.css');

    wp_localize_script('fbas-model-album','facbookAlbumsSync', fbas_generate_localized_data() );

}// end add settings


/**
 * Save settings and albums via ajax
 *
 * @since 0.6
 */
function ajax_save_settings() {

    if( isset( $_POST['validPageName'] ) && ! empty( $_POST['validPageName'] ) ){
        update_option( 'validPageName' , sanitize_text_field( $_POST['validPageName'] ) );
    }

    // save synced albums data on the settings page
    if( isset( $_POST[ 'albums' ] ) && ! empty( $_POST[ 'albums' ] ) ){
        update_option( 'fbas_synced_albums', $_POST['albums'] );
    }

}

/**
 * Render the checkbox asking people if they want to exclude albums.
 *
 * @since 0.6
 */

function fbas_checkbox_field_exclude_ablums_render(){
    $checked = '';
    $visible = '';
    $checked_setting = get_option('fbas_exclude_albums');
    if( 'on'== $checked_setting ){
        $checked  = ' checked="checked" ';

    }else{
        $visible = 'hidden';
    }

    ?>
    <input id="fbas_exclude_albums" name="fbas_exclude_albums" type="checkbox" <?php echo $checked; ?> />
    <a id="fbas-refresh" class="button <?php echo $visible; ?> " href="#">Refresh</a>
    <?php echo get_loading_gif('fbas_exclude_albums_loading'); ?>
    <?php
}//end exclude checkbox field

/**
 * Render the albums area
 *
 * @since 0.6
 */
function fbas_albums_area_render(){

    $class="";
    if( 'on'!= get_option('fbas_exclude_albums') ){
        $class ="hidden";
    }

    $synced_albums = get_option( 'fbas_synced_albums' );
    $excluded_albums = get_option( 'fbas_excluded_ids' );

    //setup the excluded ids array to compare all albums against
    $excluded_ids = array();
    if( !empty( $excluded_albums ) && is_array( $excluded_albums ) ){

        $excluded_ids = array_keys( $excluded_albums );

    }

 ?>
    <div id="fbas-albums" class="<?php echo $class ;?>" >
        <?php _e('Select the albums you want to exclude','facebook-album-sync') ?>
        <ul class="fbas-albums-list" >
            <?php
            if( !empty( $synced_albums ) && is_array( $synced_albums ) ){
                foreach( $synced_albums as $album ){

                    $id = $album['id'];
                    $album_name = $album['name'];
                    $input_name = 'fbas_excluded_ids['. $id  .']';

                    //is this albums checked
                    $checked = '';
                    if( in_array( intval( $id ), $excluded_ids )  ){
                        $checked  = ' checked="checked" ';
                    }

                    // generate the albums list item
                    $li = '';
                    $li .= '<li>';
                    $li .= '<input type="checkbox" '. $checked .'name="'. esc_attr( $input_name ) .'" id="'.esc_attr( $id) . '">';
                    $li .= '<label for="'.esc_attr( $id) . '">'. $album_name .'</label>';
                    $li .= '</li>';

                    // output list item
                    echo $li;

                }
            }
            ?>
        </ul>
    </div>

<?php
}// end albums area render
