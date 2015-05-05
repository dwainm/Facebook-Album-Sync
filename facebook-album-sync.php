<?php
/*
Plugin Name: Facebook Album Sync
Plugin URI: http://miiweb.net/plugins/facebook-album-sync
Description: Sync your Facebook Page albums with your WordPress site and load albums on any page by using short codes.
Version: 1.0
Author: Dwainm
Author URI: http://dwainm.wordpress.com
*/

// Load the plugin settings scripts
include_once('settings.php');

/**
*  Load the needed scripts
*/


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
    		wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'lightbox', $plugin_url.'js/lightbox.js', array('jquery'), '0.4', true );
			wp_enqueue_script( 'smooth_scroll',$plugin_url.'js/jquery.smooth-scroll.min.js', array('jquery'), '0.4', true );
			wp_enqueue_script( 'facebook_albums_sync', $plugin_url.'js/facebook-album-sync.js', array('jquery'), '0.4', true  );

			// place this in the javascript of the page
			wp_enqueue_style('lightbox_css',$plugin_url.'css/lightbox.css' );
			wp_enqueue_style( '1140_ie',$plugin_url.'css/ie.css' );
			wp_enqueue_style( 'fbalbumsync_mainstyle',$plugin_url.'css/fbasstyles.css' );
			wp_enqueue_script('fbalbumsync_media_query_js',$plugin_url.'js/css3-mediaqueries.js' );

       }   
	}
}    

add_action('wp_enqueue_scripts', 'my_scripts_method'); 


/**
*
*  enqueu plugin js in the footer dependant on which page we're on
*/

function enque_view_scripts(){
	
	// $url contains the path to your plugin folder
	$plugin_url = plugin_dir_url( __FILE__ );

	if( all_albums_view() ){

		wp_enqueue_script('fbas_all_albums_view',$plugin_url.'js/all-albums-view.js' );
	
	}else{
		wp_enqueue_script('fbas_single_album_view',$plugin_url.'js/single-album-view.js' );
	}

}


add_action( 'fbas_shortcode_after', 'enque_view_scripts');


/**
*
*  generate data for the licalization the variable needed 
*/

add_action('fbas_shortcode_before','generate_localized_data');

function generate_localized_data($atts){

	$data  = array( 'facebookPageName' => get_option('fbas_page') );

	if( all_albums_view() ){	

		//check if shortcode attributes excludeds any albums
		if (array_key_exists('exclude', $atts)){
			$exclude_csv_string = $atts['exclude'];
		}

		// create the array that will be localizaed
		$data['exludeAlbums']  = explode(',', $exclude_csv_string );
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

	// localize data based on the page the users viewing

	wp_localize_script( 'facebook_albums_sync' , 'facbookAlbumsSync', $data);

}

/**
* the shortcode calls this function fromt the front end
*
* @param $attts the attributes passed from the shortcode usage in the page editor
* @since 0.1 
*/

function fbas_shortcode_render($atts) {

	// alow function to hook in at this point
	do_action('fbas_shortcode_before', $atts);

	//perform check for shortcode attributes
	if( is_array($atts) ) {
		if (array_key_exists ( 'album', $atts )){
			$album_id = $atts['album'];
		}

		if (array_key_exists('exclude', $atts)){
			$exclude = $atts['exclude'];
		}
	}

    if ( all_albums_view() ){

    	// show albums
    	include('all_albums_view.php'); 

    }else{// show specific photos in an album

    	include('single_album_view.php');

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
* Determine if we're on the all albums view or on the single 
* album page
*
* @since 0.4
*/
function all_albums_view(){

		$result = (  !isset( $_REQUEST['fbasid'] ) && !isset( $album_id ) )? true : false;

	    return $result;
}

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