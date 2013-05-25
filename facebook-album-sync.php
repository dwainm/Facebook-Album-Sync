<?php
/*
Plugin Name: Facebook Albums Sync
Plugin URI: http://miiweb.net/plugins/facebook-albums-sync
Description: Sync your Facebook albums with your WordPress site and load albums on any page by using short codes.
Version: 0.1
Author: Dwainm
Author URI: http://dwainm.wordpress.com
*/

//*********** for install/uninstall actions (optional) ********************//
/*register_activation_hook(__FILE__,'facebook_albums_sync_install');
register_deactivation_hook(__FILE__, 'facebook_albums_sync_uninstall');
function facebook_albums_sync_install(){
     facebook_albums_sync_uninstall();//force to uninstall option
     add_option("facebook_albums_sync_secret", generateRandom(10));
}

function facebook_albums_sync_uninstall(){
    if(get_option('facebook_albums_sync_secret')){
     delete_option("facebook_albums_sync_secret");
     }
}*/
//*********** end of install/uninstall actions (optional) ********************//

// add scripts needed


function my_scripts_method() {

	global $post;
	if ( !empty($post) ){
        // check the post content for the short code
        if ( stripos($post->post_content, '[fbalbumsync')!==FALSE ){
            // we have found a post with the short code

			// $url contains the path to your plugin folder
			$url = plugin_dir_url( __FILE__ );
			//include javascript files
    		wp_enqueue_script( 'jquery_fbalbumsync' );
			wp_enqueue_script('lightbox',$url.'js/lightbox.js', array('jquery_fbalbumsync'), '1.0', true);
			wp_enqueue_script('smooth_scroll',$url.'js/jquery.smooth-scroll.min.js', array('jquery_fbalbumsync'), '1.0', true);
    		wp_enqueue_script( 'jquery' );
			// place this in the javascript of the page
			wp_enqueue_style('lightbox_css',$url.'css/lightbox.css' );
			wp_enqueue_style( '1140_ie',$url.'css/ie.css' );
			wp_enqueue_style( 'fbalbumsync_mainstyle',$url.'css/fbasstyles.css' );
			wp_enqueue_script('fbalbumsync_media_query_js',$url.'js/css3-mediaqueries.js' );

			
       }   
	}
}    
// add scripts to wordpress front end (hook)
add_action('wp_enqueue_scripts', 'my_scripts_method'); 

///
add_action('admin_menu', 'facebook_albums_sync_menu');

function facebook_albums_sync_menu() {
    //$pending = '<span class="update-plugins"><span class="pending-count">7</span></span>';
	//add_menu_page('Facebook Album Sync', 'Facebook Album Sync'.$pending, 'manage_options', 'facebook_albums_sync', 'facebook_albums_sync_options');
	add_options_page('Facebook Album Sync', 'Facebook Albums', 'manage_options', 'facebook_albums_sync', 'facebook_albums_sync_options');    
    //add_submenu_page( 'facebook_albums_sync', 'Super Plugin', 'Settings', 'manage_options', 'super_plugin_unique_url', 'facebook_albums_sync_options');
    
}

function super_plugin_unique_url(){
  	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	echo '<div class="wrap">';
    echo '<h2>This is Settings Page</h2>';
	echo '<p>Include PHP file for better readability of your code.</p>';
	echo '</div>';

}

function facebook_albums_sync_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	echo '<div class="wrap">';
    echo '<h2>Facebook Albums Sync Settings</h2>';
    include('settings.php');
	echo '</div>';
}


//Add Shortcodes
function fbalbumsync_func($atts) {

    if (get_query_var('fbasid')=="")
    {
    	//if permalink sturcture is not default
    	$curLink = get_permalink();
    	if (!strpos($curLink, '?')===false ){
    		$prettypermalinkon = "0";
    		}else{
    		$prettypermalinkon = "1";
    		}
    	// show albums
    	include('albums.php'); 
    }else{// show specific photos in an album
    	include('photos.php');
    }
}
add_shortcode('fbalbumsync', 'fbalbumsync_func');

//URL AND QUERY DETAILS
//add new permalink structure
//add_filter( 'rewrite_rules_array','fbas_rewrite_rules' );
add_filter('query_vars', 'add_my_var');
//add_action( 'wp_loaded','fbas_flush_rules' );

/* flush_rules() if our rules are not yet included
function fbas_flush_rules(){
	$rules = get_option( 'rewrite_rules' );

	if ( ! isset( $rules['^([^/]*) (project)/(\d*)$'] ) ) {
		global $wp_rewrite;
	   	$wp_rewrite->flush_rules();
	}
}*/

/* Adding a new rule
function fbas_rewrite_rules( $rules )
{
	$newrules = array();
	$newrules['^([^/]*)/album/([^/]*) '] = 'index.php?page_id=$matches[1]&fbasid=$matches[2]';
	return $newrules + $rules;
}*/

// add our var to the query
function add_my_var($public_query_vars) {
	$public_query_vars[] = 'fbasid';
	return $public_query_vars;
}

