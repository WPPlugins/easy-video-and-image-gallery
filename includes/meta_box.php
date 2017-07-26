<?php
defined( 'ABSPATH' ) or die();


add_action( 'load-post.php', 'evigallery_meta_boxes_setup' );
add_action( 'load-post-new.php', 'evigallery_meta_boxes_setup' ); 

function evigallery_meta_boxes_setup() {
	add_action( 'add_meta_boxes', 'evigallery_add_meta_boxes' );
	add_action( 'save_post', 'evigallery_save_meta', 10, 2 );
}
function evigallery_add_meta_boxes() {
	add_meta_box( 'evigallery-box', __( 'Gallery items', 'evigallery' ), 'evigallery_meta_box', 'evigallery', 'normal', 'high' );
}
function evigallery_meta_box( $object, $box ) {
	global $post;
	add_thickbox();
	$items = get_post_meta($post->ID, 'evigallery-items-ids',true);
	$json_items = json_encode($items);
	?>
	<div class="inside">
		<div id="messageGallery"></div>
		<p><?php _e('Add images or videos to the gallery', 'evigallery' );?></p>
		<input type="hidden" name="evigallery-items-ids" id="evigallery-items-ids" value="<?php echo esc_attr($json_items);?>"/>
		<label id="button-photo">
			<input type="button" id="evigallery-add-file" value="<?php _e('Add images', 'evigallery'); ?>" class="button button-primary button-large" />
		</label>
		<label id="button-video">
			<a href="#TB_inline?width=400&height=400&inlineId=modal-window-id" id="evigallery-add-video" class="button button-primary button-large thickbox" /><?php _e('Add video', 'evigallery'); ?></a>
		</label>
		<div id="modal-window-id" style="display:none;">
			<table class="table-evigallery">
				<tr><td>Titre</td><td><input type="text" id="evigallery-video-title" value="" size="60" /></label></td></tr>
				<tr><td>URL</td><td><input type="text" id="evigallery-video-url" value="" size="60"  />
				<br><span><?php _e('Set an URL like http://www.youtube.com/watch?v=XXXX', 'evigallery'); ?>: </span></td></tr>
				<tr><td></td><td>
				<input type="button" id="evigallery-video-url-add" value="<?php _e('Add video', 'evigallery'); ?>" class="button button-primary button-large" />
				</td></tr>
			</table>
		</div>
		<div id="evigallery-items"><?php
		if(is_array($items))
		{
			foreach($items as $item)
			{
				$id = intval($item['id_att']);
				if($item['type'] === 'image')
				{
					$img = wp_get_attachment_image_src( $id,'thumbnail' );
					if(!empty($img[0]))
					{
						?><div class="evigallery-thumb"><img src="<?php echo esc_url($img[0]);?>" data-type="image" data-idatt="<?php echo $id;?>"><a href="#" class="evigallery-close media-modal-icon" title="<?php _e('Delete', 'evigallery'); ?>"></a></div><?php
					}
				}
				elseif($item['type'] === 'video')
				{
					$img = wp_get_attachment_image_src( $item['id_att'], 'thumbnail');
					?><div class="evigallery-thumb"><img src="<?php echo esc_url($img[0]);?>" data-type="video" data-prov="<?php echo $item['prov'];?>" data-idatt="<?php echo $id;?>" data-vid="<?php echo esc_attr($item['vid']);?>"><a href="#" class="evigallery-close media-modal-icon" title="<?php _e('Delete', 'evigallery'); ?>"></a></div><?php
				}
			}
		}
		?><div class="evigallery-thumb evigallery-thumb-load hidden"><img src="<?php echo includes_url('/images/spinner.gif');?>" alt=""></div></div>

	</div>
	<?php
}

add_action( 'wp_ajax_evigallery_ajax_add_video', 'evigallery_ajax_add_video_callback' );
function evigallery_ajax_add_video_callback() {

	$vid = $_POST['vid'];
	$title = $_POST['title'];
	
	if(evigallery_is_valid_youtube_id($vid))
	{
		$args = array(
			'post_type'     => 'attachment',
			'post_per_page' => -1,
			'post_status' => array( 'publish', 'inherit' ),
			'meta_key'   => 'evigallery-video-id',
			'meta_query' => array (
				array (
					'key' => 'evigallery-video-id',
					'value' => 'youtube-'.$vid,
					'compare' => 'LIKE',
				)
			)
		);
		$get_att = new WP_Query( $args );
		if(count($get_att->posts))
		{
			$id = $get_att->posts[0]->ID;
		}
		else
		{
			$url = evigallery_get_youtube_thumb($vid);
			$tmp = download_url( $url );
			$file_array = array(
				'name' => sanitize_title($title).'.jpg',
				'tmp_name' => $tmp
			);
			if ( is_wp_error( $tmp ) ) {
				@unlink( $file_array[ 'tmp_name' ] );
				return $tmp;
			}
			$id = media_handle_sideload( $file_array, 0, $title );
			if ( is_wp_error( $id ) ) {
				@unlink( $file_array['tmp_name'] );
				return $id;
			}
			update_post_meta( $id, 'evigallery-video-id', 'youtube-'.$vid );
		}
		
		
		$img = wp_get_attachment_image_src( $id,'thumbnail' );
		echo '[{"type":"video","prov":"youtube","id_att":'.$id.',"vid":"'.$vid.'"},"'.$img[0].'"]';
	}
	wp_die();
}

function evigallery_save_meta( $post_id, $post ) {
	$items = json_decode(stripslashes($_POST['evigallery-items-ids']));
	$items_data = [];
	for($i=0; $i<count($items); $i++)
	{
		$item = $items[$i];
		$val = abs(intval($item->id_att));
		if($item->type === 'image')
		{
			if($val > 0)
				$items_data[] = array("type"=>"image", "id_att"=>$val);
		}
		else if($item->type === 'video')
		{
			if($val > 0 && evigallery_is_valid_youtube_id($item->vid))
				$items_data[] = array(
					"type" => "video",
					"prov" => "youtube",
					"id_att" => $val,
					"vid" => $item->vid
				);
		}
	}
	update_post_meta($post->ID, 'evigallery-items-ids', $items_data );
}

