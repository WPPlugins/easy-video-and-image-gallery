<?php
defined( 'ABSPATH' ) or die();

function evigallery_get_youtube_id($url)
{
	parse_str( parse_url( $url, PHP_URL_QUERY ), $vars );
	return $vars['v']; 
}
function evigallery_get_youtube_thumb($vid)
{
	return 'http://img.youtube.com/vi/'.$vid.'/0.jpg';
}

function evigallery_is_valid_youtube_id($id)
{
    return preg_match('/^[a-zA-Z0-9_-]{11}$/', $id) > 0;
}

