<?php
defined( 'ABSPATH' ) or die();



add_action('media_buttons', 'evigallery_editor_btn', 19);
add_action('admin_footer', 'evigallery_editor_content');


//action to add a custom button to the content editor
function evigallery_editor_btn($context) {
  
	//append the icon
	?><button type="button" id="evigallery-mce-btn" class="button" title="<?php _e('Gallery Soliles', 'evigallery') ?>"><span></span></button><?php
}
function evigallery_editor_content()
{
	$albums = get_terms( 'evigallery_album', 'hide_empty=0' );
	$args = array(
			'posts_per_page' => 0,
			'orderby' => 'date',
			'post_type' => 'evigallery',
			'post_status' => 'publish'
	);
	$galleries = get_posts( $args );
	?>
<div id="evigallery-mce-content" style="display:none;">
	<p><?php _e('View type', 'evigallery') ?> :
		<label><input type="radio" name="evigallery-viewmode" id="evigallery-viewmode-gallery" value="gallery" checked> <?php _e('Gallery', 'evigallery') ?></label>
		<label><input type="radio" name="evigallery-viewmode" id="evigallery-viewmode-album" value="album"> <?php _e('Album', 'evigallery') ?></label>
	</p>
	
	
	<div id="evigallery-form">
<?php 
		if(is_array($galleries) && count($galleries))
		{
			?>
		<select id="evigallery-choose" data-placeholder="<?php _e('Choose a Gallery', 'evigallery') ?>" name="evigallery-choose" class="lcweb-chosen" autocomplete="off" style="width: 350px;">
		<?php 
		foreach ( $galleries as $gallery ) {
			echo '<option value="'.$gallery->ID.'">'.$gallery->post_title.'</option>';
		}
		?>
		</select>
		<p><button id="evigallery-insert-gallery" class="button button-primary button-large"><?php _e('Insert', 'evigallery') ?></button></p>
			<?php
		}
		else
		{
			?><p><?php _e('No Galleries found', 'evigallery') ?></p><?php
		}
?>
	</div>
	
	<div id="evigallery-album-form" style="display:none">
<?php 
		if(is_array($albums) && count($albums)) 
		{
			?>
		<select id="evigallery-album-choose" data-placeholder="<?php _e('Choose an Album', 'evigallery') ?>" name="evigallery-album-choose" class="lcweb-chosen" autocomplete="off" style="width: 350px;">
		<?php 
		foreach ( $albums as $album ) {
			echo '<option value="'.$album->term_id.'">'.$album->name.'</option>';
		}
		?>
		</select>
		<p><button id="evigallery-insert-album" class="button button-primary button-large"><?php _e('Insert', 'evigallery') ?></button></p>
			<?php
		}
		else
		{
			?><p><?php _e('No Album found', 'evigallery') ?></p><?php
		}
		
?>
	</div>
</div>
	<?php
}