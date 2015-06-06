<?php
/**
 Plugin Name: Facebook Album Sync
 Plugin URI: https://github.com/dwainm/Facebook-Album-Sync
 Description: Sync your Facebook Page albums with your WordPress site and load albums on any page by using short codes.
 Version: 0.5
 Author: Dwain Maralack
 Author URI: http://dwainm.com
 License: GPL2
 Network: true
 */

// Load the plugin settings scripts
include_once('includes/settings.php');

// Set the the plugin up to load the all albums page by default
global $fbas_is_all_albums_page;
$fbas_is_all_albums_page = true;

/**
*  Load the needed scripts
*/
function fbas_version(){

	$plugin_version = '0.6';
	return $plugin_version;

}// end version

/**
 * Return the plugin url
 *
 * @since 0.6
 */
function fbas_get_plugin_url(){
    // $url contains the path to your plugin folder
    return plugin_dir_url( __FILE__ );
}//

function my_scripts_method() {

	global $post;
	if ( !empty($post) ){

        // check the post content for the short code
        if ( stripos($post->post_content, '[fbalbumsync')!==FALSE || 
        	 stripos($post->post_content, '[facbook_albums')!==FALSE || 
        	 stripos($post->post_content, '[fbalbumssync')!==FALSE 		){

			// $url contains the path to your plugin folder
			$plugin_url = plugin_dir_url( __FILE__ );

			//include javascript files
			wp_enqueue_script( 'lightbox', $plugin_url.'js/lib/lightbox.js', array('jquery'), '0.4', true );
			wp_enqueue_script( 'smooth_scroll',$plugin_url.'js/lib/jquery.smooth-scroll.min.js', array('jquery'), '0.4', true );
			wp_enqueue_script( 'facebook_albums_sync', $plugin_url.'js/facebook-album-sync.js', array('jquery','underscore','backbone'), '0.4', true  );

			// place this in the javascript of the page
			wp_enqueue_style('lightbox_css',$plugin_url.'css/lightbox.css' );
			wp_enqueue_style( '1140_ie',$plugin_url.'css/ie.css' );
			wp_enqueue_style( 'fbalbumsync_mainstyle',$plugin_url.'css/fbasstyles.css' );
			wp_enqueue_script('fbalbumsync_media_query_js',$plugin_url.'js/lib/css3-mediaqueries.js' );

       }   
	}
} // end my_scripts_method     

add_action('wp_enqueue_scripts', 'my_scripts_method');



/**
 *  enqueue plugin js in the footer dependant on which page we're on.
 *  This runs in production when when SCRIPT_DEBUG global is disabled.
*/
function enqueue_view_scripts(){

    if( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ){
        return;
    }
	// $url contains the path to your plugin folder
	$plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_script( 'fbas-react',$plugin_url.'js/lib/react/react.min.js', array(), fbas_version(), true );

    global $fbas_is_all_albums_page;
	if( $fbas_is_all_albums_page ){
		// load the album model file that contains the logic for fetching albums from facebook.
		wp_enqueue_script('fbas-model-album',$plugin_url.'js/models/album.js', array('jquery','underscore','backbone', 'fbas-react'), fbas_version() , true );
		wp_enqueue_script('fbas_all_albums_view',$plugin_url.'js/views/all-albums.js', array('jquery','underscore','backbone', 'fbas-react'), '0.4', true );
	
	}else{
        wp_enqueue_script('fbas-model-photo',$plugin_url.'js/models/photo.js', array('jquery','underscore','backbone', 'fbas-react'), fbas_version() , true );
		wp_enqueue_script('fbas_single_album_view',$plugin_url.'js/views/single-album.js', array('jquery','underscore','backbone', 'fbas-react'), '0.4', true );
	}

}
add_action( 'fbas_shortcode_after', 'enqueue_view_scripts');

/**
* This function is to use react in developer mode, when SCRIPT_DEBUG global is
 * defined. It prints out the jsx compiler and the jsx scripts.
*/
add_action( 'wp_footer', 'print_dev_jsx_scripts', 80);

function print_dev_jsx_scripts(){

	// $url contains the path to your plugin folder
	$plugin_url = plugin_dir_url( __FILE__ );

    // Load Developer Scripts
    if( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ){

        // load the jsx compiler script
        echo _fbas_generate_script( $plugin_url.'js/lib/react/react.js' );
        echo _fbas_generate_script( $plugin_url.'js/lib/react/react-jsx.js' );

        global $fbas_is_all_albums_page;
        if( $fbas_is_all_albums_page ){

            // load the album model file that contains the logic for fetching albums from facebook.
            echo _fbas_generate_script( $plugin_url.'js/models/album.js' );
            echo _fbas_generate_script( $plugin_url.'js/views/all-albums.jsx', 'text/jsx' );

        }else{

            echo _fbas_generate_script( $plugin_url.'js/models/photo.js' );
            echo _fbas_generate_script( $plugin_url.'js/views/single-album.jsx', 'text/jsx' );

            wp_enqueue_script('fbas_single_album_view',$plugin_url.'js/views/single-album.js', array('jquery','underscore','backbone', 'fbas-react'), '0.4', true );
        }

    }
}



/**
* Create a script tag for the given script url and type
* only to be used in development. For product use wp_enqueue_script
*/
function _fbas_generate_script( $url, $type = 'text/javascript' ){

	return "<script type=\"$type\" src=\"$url\"></script>";

}

/**
*
*  generate data for the licalization the variable needed 
*/

add_action('fbas_shortcode_before','fbas_generate_localized_data');

function fbas_generate_localized_data($atts = array() ){

	$data  = array( 'facebookPageName' => get_option('fbas_page') );

    global $fbas_is_all_albums_page;
	if( $fbas_is_all_albums_page ){
		//
		// create the array that will be localizaed
		//

        // exclude albums
        $excluded_setting = get_option( 'fbas_excluded_ids' );
		if ( isset( $atts['exclude'] ) && array_key_exists('exclude', $atts ) ){

			//check if shortcode attributes excludeds any albums
			$exclude_csv_string = $atts['exclude'];
			$data['exludeAlbums']  = explode(',', $exclude_csv_string );

		}elseif( ! empty( $excluded_setting ) ){

            if( is_array( $excluded_setting )  ){
                foreach( $excluded_setting  as $id => $setting ){
                    $data['exludeAlbums'][] = $id;
                }
            }else{
                $data['exludeAlbums'][] =  $excluded_setting;
            }


        }
		$data['prettyPermalinks'] =   is_pretty_permalinks_on(true); //true tells the function to return string
		$data['success'] = 'true'; 

	}else{

		if( isset($_REQUEST['fbasid'] ) ){

			$data['albumId'] = $_REQUEST['fbasid'];
			$data['singleAlbumShortcode'] = 'true'; 
			$data['success'] = 'true';
		
		}else{
		
			//do nothing as the album id didn't come through
			$data['success'] = 'false'; 

		}

	}

    $data['validPageName'] =  get_option( 'validPageName' );

	// localize data based on the page the users viewing
	wp_localize_script( 'facebook_albums_sync' , 'facbookAlbumsSync', $data);
    return $data;

}

/**
* the shortcode calls this function fromt the front end
*
* @param $attts the attributes passed from the shortcode usage in the page editor
* @since 0.1 
*/

function fbas_shortcode_render( $atts ) {

	// alow function to hook in at this point
	do_action('fbas_shortcode_before', $atts);



	//perform check for shortcode attributes
	if( is_array($atts) ) {
		if (array_key_exists ( 'album', $atts )){
            global $fbas_album_id;
			$fbas_album_id = $atts['album'];
		}

		if (array_key_exists('exclude', $atts)){
			$exclude = $atts['exclude'];
		}
	}

    // Define the $fbas_is_all_albums_page ::: Please not this is used later when printing out the scripts
    global $fbas_is_all_albums_page;
    $fbas_is_all_albums_page =  ! ( isset( $_REQUEST['fbasid'] ) || isset( $fbas_album_id ) );

    // if there is a query param `fbasid` or the $album_id is set via the shortcode
    if ( $fbas_is_all_albums_page ){

        // default show albums
        include('templates/all-albums.php');

    }else{

        // show specific photos in an album
        include('templates/single-album.php');

    }

    do_action('fbas_shortcode_after');
}

add_shortcode('fbalbumsync', 'fbas_shortcode_render');
add_shortcode('fbalbumssync', 'fbas_shortcode_render');
add_shortcode('facbook_albums', 'fbas_shortcode_render');

/**
* add our var to the query
*
* @since 0.1 
*/
function add_my_var($public_query_vars) {
	$public_query_vars[] = 'fbasid';
	return $public_query_vars;
}
add_filter('query_vars', 'add_my_var');

/**
* is_pretty_permalinks_on check the wordpress permalink structure and return true 
* if we're using pretty permalinks. Returns string valu
* @since 0.4 
* @param bool $return_as_string
* @return string $result 
*/
function is_pretty_permalinks_on ($return_as_string=false){

    global $wp_rewrite;

    $result = false;

    if ($wp_rewrite->permalink_structure == ''){
        $result =  $return_as_string? 'false' : false; //we are using ?page_id
    }else{
        $result = $return_as_string? 'true' : true;
    }

    return $result;
}