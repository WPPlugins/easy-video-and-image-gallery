<?php
defined( 'ABSPATH' ) or die();


function evigallery_settings_page() {	
	add_submenu_page('edit.php?post_type=evigallery', __('Settings', 'evigallery'), __('Settings', 'evigallery'), 'install_plugins', 'evigallery_settings', 'evigallery_settings');	
}
add_action('admin_menu', 'evigallery_settings_page');

function evigallery_settings() {
	$default_width = get_option( "thumbnail_size_w" );
	$default_height = get_option( "thumbnail_size_h" );
	
	$oldwidth = intval(get_option('evigallery-thumb-width', $default_width));
	$oldheight = intval(get_option('evigallery-thumb-height', $default_height));
	
	if(isset($_POST['evigallery-thumb-width']) && intval($_POST['evigallery-thumb-width']))
		update_option('evigallery-thumb-width', intval($_POST['evigallery-thumb-width']));
	if(isset($_POST['evigallery-thumb-height']) && intval($_POST['evigallery-thumb-height']))
		update_option('evigallery-thumb-height', intval($_POST['evigallery-thumb-height']));
	
	$width = intval(get_option('evigallery-thumb-width', $default_width));
	$height = intval(get_option('evigallery-thumb-height', $default_height));

	?>
<div class="wrap">
	<h2><?php _e('Settings', 'evigallery' );?></h2>
	<form id="evigallery-form" name="evigallery-form" method="post">
		
		<h3><?php _e('Size of thumbnails', 'evigallery' );?></h3>
		<table>
			<tr>
				<td><label for="evigallery-thumb-width"><?php _e('Width in pixels', 'evigallery' );?></label></td>
				<td><input type="text" id="evigallery-thumb-width" name="evigallery-thumb-width" value="<?php echo $width;?>" size="5">
			</tr>
			<tr>
				<td><label for="evigallery-thumb-height"><?php _e('Height in pixels', 'evigallery' );?></label></td>
				<td><input type="text" id="evigallery-thumb-height" name="evigallery-thumb-height" value="<?php echo $height;?>" size="5">
			</tr>
		</table>
		
		<?php submit_button(); ?>
		
		<hr>
<?php
	if ($oldwidth != $width || $oldheight != $height):
	?><p><?php _e('The size of your thumbnails have changed. You have to recreate them by clicking the recreate button', 'evigallery' );?></p><?php endif;?>
		<input type="hidden" id="dorecreatethumb" name="dorecreatethumb" value="0">
		<button class="button button-primary" id="recreatethumb-btn"><?php _e('Recreate all thumbnails', 'evigallery' );?></button>
	
	<?php
	if( isset($_POST['dorecreatethumb']) && $_POST['dorecreatethumb'])
	{
		global $wpdb;
		$posts = $wpdb->get_results("select ID from $wpdb->posts where post_type = 'attachment' and post_mime_type like 'image/%'");
		if(count($posts))
		{
			$ids = array();
			foreach($posts as $post) {$ids[] = $post->ID;}
			?><div id="recreatethumb-progress"><div></div></div><div id="message"></div><script type="text/javascript">
// <![CDATA[
jQuery(document).ready(function($){
	var images_ids = [<?php echo implode(',',$ids);?>];
	var total = <?php echo count($ids);?>;
	var done = 0;
	function update_progress()
	{
		$('#recreatethumb-progress div').css({ "width":(100*done/total)+"%"});
	}
	function recreatethumb()
	{
		$('#recreatethumb-progress').show();
		var id = images_ids.shift();
		var arg = {
			'action': 'evigallery_recreate_thumb',
			'id': id
		};
		$.post(evigallery_object.ajax_url, arg, function(data) {
			done++;
			update_progress();
			if(done<total)
			{
				recreatethumb();
			}
			else
			{
				$('#recreatethumb-progress').hide();
				$('#message').html('<h3>Done!</h3>');
			}
		});
	}
	recreatethumb();
});
// ]]>
</script><?php
			
		}
	}
	?>
	</form>
</div>
	<?php

}


add_action( 'wp_ajax_evigallery_recreate_thumb', 'evigallery_ajax_recreate_thumb_callback' );
function evigallery_ajax_recreate_thumb_callback() {
	$id = intval($_POST['id']);
	
	$path = get_attached_file($id);
	if(file_exists($path))
	{
		$data = wp_generate_attachment_metadata($id, $path);
		wp_update_attachment_metadata($id, $data);
	}
	
}