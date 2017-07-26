<?php
defined( 'ABSPATH' ) or die();


add_action( 'init', 'evigallery_create_post_type' );
function evigallery_create_post_type() {
	$labels = array(
		'name' => __('Galleries', 'evigallery'),
		'singular_name' => __('Gallery','evigallery'),
		'add_new' => __('Add New', 'evigallery'),
		'add_new_item' => __('Add New Gallery', 'evigallery'),
		'edit_item' => __('Edit Gallery', 'evigallery'),
		'new_item' => __('New Gallery', 'evigallery'),
		'view_item' => __('View Gallery', 'evigallery'),
		'search_items' => __('Search Galleries', 'evigallery'),
		'not_found' =>  __('No Galleries found', 'evigallery'),
		'not_found_in_trash' => __('No Galleries found in the trash', 'evigallery'), 
		'parent_item_colon' => __('Parent Gallery:', 'evigallery')
	);

	$args = array(
		'labels' => $labels,
		'singular_label' => __('Gallery', 'evigallery'),
		'public' => false,
		'publicly_queriable' => true,
		'show_ui' => true,
		'exclude_from_search' => true,
		'show_in_nav_menus' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'has_archive' => false,
		'rewrite' => false,
		'menu_position' => 5,
		'supports' => array('title'),// 'thumbnail'),
		'menu_position'	=>	5,
		'menu_icon'   => 'dashicons-screenoptions'
	);

	register_post_type( 'evigallery' , $args );
	
	// Discography > Sections Taxonomy
	$labels = array(
		'name' => __( 'Albums', 'evigallery' ),
		'singular_name' => __( 'Album', 'evigallery' ),
		'search_items' =>  __( 'Search Gallery Album', 'evigallery' ),
		'all_items' => __( 'All Gallery Album', 'evigallery' ),
		'parent_item' => __( 'Parent Gallery Album', 'evigallery' ),
		'parent_item_colon' => __( 'Parent Gallery Album:', 'evigallery' ),
		'edit_item' => __( 'Edit Gallery Album', 'evigallery' ), 
		'update_item' => __( 'Update Gallery Album', 'evigallery' ),
		'add_new_item' => __( 'Add New Gallery Album', 'evigallery' ),
		'new_item_name' => __( 'New Gallery Album', 'evigallery' ),
		'menu_name' => __( 'Albums of galleries', 'evigallery' ),
	);

	register_taxonomy('evigallery_album', array('evigallery'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => false,
	));
}