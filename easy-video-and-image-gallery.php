<?php
/*
Plugin Name: Easy Video and Image Gallery
Description: Plugin for managing galleries of photos and videos and albums of galleries
Version: 1.0.1
Author: soliles
Author URI: http://www.soliles.fr
Text Domain: evigallery
Domain Path: languages/
Requires at least: 3.8
Tested up to: 4.5
Released under the GNU General Public License (GPL)
http://www.gnu.org/licenses/gpl.txt

*/
defined( 'ABSPATH' ) or die();

define( 'EVIGALLERY_PLUGIN_PATH' , substr(plugin_dir_path(__FILE__), 0, -1));
define( 'EVIGALLERY_PLUGIN_URL' , substr(plugin_dir_url(__FILE__), 0, -1));

add_action( 'plugins_loaded', 'evigallery_load_textdomain' );
function evigallery_load_textdomain() {
	load_plugin_textdomain( 'evigallery', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
}

function evigallery_add_image_size()
{
	$width = intval(get_option('evigallery-thumb-width', '150'));
	$height = intval(get_option('evigallery-thumb-height', '150'));
	add_image_size('evigallery-thumb',$width,$height,true);
}
evigallery_add_image_size();

include_once( EVIGALLERY_PLUGIN_PATH . '/includes/helpers.php' );
include_once( EVIGALLERY_PLUGIN_PATH . '/includes/post_type.php' );
include_once( EVIGALLERY_PLUGIN_PATH . '/includes/meta_box.php' );
include_once( EVIGALLERY_PLUGIN_PATH . '/includes/script_and_style.php' );
include_once( EVIGALLERY_PLUGIN_PATH . '/includes/shortcodes.php' );
include_once( EVIGALLERY_PLUGIN_PATH . '/includes/tinymce.php' );
include_once( EVIGALLERY_PLUGIN_PATH . '/includes/settings.php' );

