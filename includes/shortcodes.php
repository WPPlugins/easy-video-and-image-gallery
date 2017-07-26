<?php
defined( 'ABSPATH' ) or die();

function evigallery_format_title($title)
{
	return '<div class="evigallery-items-title"><div>'.$title.'</div></div>';
}
function evigallery_display_element($att)
{
	$data = array(
		"front" => "",
		"hidden" => ""
	);
	$cid = intval($att['cid']);
	$title = (isset($att['title']))? $att['title'] : '';
	
	$prefix = ($att['item']['type']=="album")? "a".$cid."-" : "g".$cid."-";
	$id_gallery = '' . $prefix . (intval($att['inc_gal'])+1);
	$id_div = $prefix . $att['inc_item'];
	
	$id = intval($att['item']['id_att']);
	$img = wp_get_attachment_image_src( $id,'evigallery-thumb' );
	
	if($att['item']['type'] == 'image')
	{
		
		if($title)
			$title = evigallery_format_title($title);
		else
			$title = evigallery_format_title(get_the_title( $id ));
		$imgbig = wp_get_attachment_image_src( $id,'full' );
		
		if(!empty($img[0]))
		{
			if($att['show'])
				$data['front'] .= '<a href="#inline-'.$id_div.'" class="evigallery-items gal-'.$id_gallery.'" id="'.$id_gallery.'"><img src="'.esc_url($img[0]).'" alt="" width="'.$img[1].'" height="'.$img[2].'">'.$title.'</a>';
			else
				$data['hidden'] .= '<a href="#inline-'.$id_div.'" class="gal-'.$id_gallery.'"><img src="'.esc_url($img[0]).'" alt=""></a>';
		}
		$data['hidden'] .= '<div id="inline-'.$id_div.'"><img src="'.esc_url($imgbig[0]).'" alt="" width="'.intval($imgbig[1]).'" height="'.intval($imgbig[2]).'" class="evigallery-photo"></div>';
	}
	else
	{
		$title =  evigallery_format_title(get_the_title( $att['item']['id_att'] ));
		$vid = $att['item']['vid'];
		if($att['show'])
		{
			$data['front'] .= '<a href="#inline-'.$id_div.'" class="evigallery-items gal-'.$id_gallery.'" id="'.$id_gallery.'"><img src="'.esc_url($img[0]).'" alt="" width="'.$img[1].'" height="'.$img[2].'">'.$title.'</a>'."\n";
			
		}else{
			$data['hidden'] .= '<a href="#inline-'.$id_div.'" class="gal-'.$id_gallery.'"><img src="'.esc_url($img[0]).'" alt=""></a>'."\n";
		}
			
		$data['hidden'] .= '<div id="inline-'.$id_div.'"><iframe width="640" height="480" frameborder="0" src="https://www.youtube.com/embed/'.$vid.'?enablejsapi=1&autoplay=0&cc_load_policy=0&iv_load_policy=1&loop=0&modestbranding=0&rel=1&showinfo=1&playsinline=0&controls=2&autohide=2&theme=dark&color=red&wmode=opaque&vq=&" allowfullscreen class="evigallery-video-iframe"></iframe></div>';
	}
	return $data;
}

add_shortcode('evigallery', 'evigallery_shortcode');
function evigallery_shortcode($atts, $content = null)
{
	extract( shortcode_atts( array(
		'id'	=> '',
		'view' => 'gallery',
		), $atts ) );
		  
	switch($view)
	{
		case 'album':
			$output = evigallery_shortcode_album($id);
			break;
		
		case 'gallery':
		default:
			$output = evigallery_shortcode_gallery($id);
			break;
	}
	return '<div id="evigallery-main"><div class="evigallery-container">'.$output['front'].'</div><div class="evigallery-inline-items">'.$output['hidden'].'</div></div>';
}

function evigallery_shortcode_gallery($id)
{
	$gid = intval($id);
	if(!$gid)
		return '';
	
	$items = get_post_meta($gid, 'evigallery-items-ids',true);
	$front = '';
	$hidden = '';
	$inc_item = 0;
	for($j=0; $j<count($items);$j++)
	{
		$inc_item++;
		$item = $items[$j];
		$eltData = evigallery_display_element(array(
			"type" => "gallery",
			"cid" => $gid,
			"item"=> $item,
			"inc_gal"=> 0,
			"inc_item"=>$inc_item,
			"show"=>true
		));
		$front .= $eltData['front'];
		$hidden .= $eltData['hidden'];
	}
	return array('front'=>$front, 'hidden'=>$hidden);
}

function evigallery_shortcode_album($id)
{
	$aid = intval($id);
	if(!$aid)
		return '';
	
	$term = get_term_by( 'id', $aid, 'evigallery_album' );
	
	$galleries = [];
	$posts = new WP_Query(array('post_type' => 'evigallery', 'evigallery_album' => $term->slug));
	while ($posts->have_posts()){ 
		$posts->the_post();
		$galleries[] = get_the_ID();
	}
	wp_reset_query();
	
	$front = '';
	$hidden = '';
	$inc_item = 0;
	for($i=0; $i<count($galleries); $i++)
	{
		$items = get_post_meta($galleries[$i], 'evigallery-items-ids',true);
		for($j=0; $j<count($items);$j++)
		{
			$inc_item++;
			$item = $items[$j];
			$show = (!$j)? true: false;
			$arg = array(
				"type" => "album",
				"cid" => $aid,
				"item"=> $item,
				"inc_gal"=> $i,
				"inc_item"=>$inc_item,
				"show"=>$show
			);
			if($show)
				$arg['title'] = get_the_title( $galleries[$i] );
			$eltData = evigallery_display_element($arg);
			$front .= $eltData['front'];
			$hidden .= $eltData['hidden'];
		}
	}
	return array('front'=>$front, 'hidden'=>$hidden);
}


