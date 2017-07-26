<?php
defined( 'ABSPATH' ) or die();

add_action( 'admin_enqueue_scripts', 'evigallery_scripts' );
function evigallery_scripts($hook){
	global $wp_version;
	
    if( !is_admin() && 'post.php' != $hook && 'post-new.php' != $hook ) {
        return;
    }
    wp_enqueue_media();

	if(version_compare($wp_version, '3.5', '>='))
	{
		wp_enqueue_script('jquery-ui-sortable');
	}
	
    wp_enqueue_script( 'evigallery-script', EVIGALLERY_PLUGIN_URL.'/js/jquery.evigallery-admin.js', array('jquery') );

    $data = array( 
		'title' => __( 'Choose a media', 'evigallery' ), 
		'button' => __('Choose a media', 'evigallery'),
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'duplivid' => __( 'This video allready exists in this gallery', 'evigallery'),
		'dupliimg' => __( 'Some images are allready in this gallery', 'evigallery'),
	);
    wp_localize_script( 'evigallery-script', 'evigallery_object', $data );
}

add_action('admin_enqueue_scripts','evigallery_admin_theme_styles');
function evigallery_admin_theme_styles() 
{
	if(is_admin())
		wp_enqueue_style('evigallery-panel-styles',EVIGALLERY_PLUGIN_URL.'/css/evigallery-admin.css');
}

add_action('init','evigallery_script_and_styles');
function evigallery_script_and_styles() 
{
	if(!is_admin())
	{
		wp_enqueue_style('evigallery-styles',EVIGALLERY_PLUGIN_URL.'/css/evigallery.css');
		wp_enqueue_script( 'colorbox-script', EVIGALLERY_PLUGIN_URL.'/js/jquery.colorbox-min.js', array('jquery') );
		wp_enqueue_script( 'evigallery-script', EVIGALLERY_PLUGIN_URL.'/js/jquery.evigallery.js', array('jquery') );
	}
}

add_action( 'wp_enqueue_scripts', 'evigallery_custom_styles' );
function evigallery_custom_styles()
{
	if(!is_admin())
	{
		$default_width = get_option( "thumbnail_size_w" );
		$default_height = get_option( "thumbnail_size_h" );
		$width = intval(get_option('evigallery-thumb-width', $default_width));
		$height = intval(get_option('evigallery-thumb-height', $default_height));
		$custom_css = "#evigallery-main .evigallery-items{width:".$width."px;height:".$height."px;} #evigallery-main .evigallery-items-title{width:".$width."px;height:".$height."px;left:".$width."px;}";
        wp_add_inline_style( 'gallery-styles', $custom_css );
	}
}
