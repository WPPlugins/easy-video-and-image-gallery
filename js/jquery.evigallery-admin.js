jQuery(document).ready(function($){
	
	function evigallery_searchItem(items,item)
	{
		for(var i=0; i<items.length; i++)
		{
			if(items[i].type == item.type)
			{
				if(item.type == "image" && item.id_att == items[i].id_att)
					return 1;
				else if(item.type == "video" && item.vid == items[i].vid)
					return 1;
			}
		}
		return 0;
	}

	function evigallery_addMedia(att)
	{
		$('#messageGallery').html('');
		var field = $("#evigallery-items-ids");
		if(field.val() !== "")
			var items = $.parseJSON(field.val());
		else
			var items = [];
		var item = {'type':'image','id_att':att.id};
		if(!evigallery_searchItem(items,item))
		{
			items.push(item);
			field.val(JSON.stringify(items));
			$("#evigallery-items").append('<div class="evigallery-thumb"><img src="'+att.sizes.thumbnail.url+'" data-type="image" data-idatt="'+att.id+'"><a href="#" class="evigallery-close media-modal-icon" title="Delete"></a></div>');
		}
		else
		{
			$('#messageGallery').html('<div class="evigallery-msg-gal evigallery-msg-gal-error">'+evigallery_object.dupliimg+'</div>');
		}
	}
	
	function evigallery_addVideo(id,title)
	{
		$("#evigallery-items .evigallery-thumb-load").removeClass("hidden");
		$('#messageGallery').html('');
		var data = {
			'action': 'evigallery_ajax_add_video',
			'vid': id,
			'title': title
		};
		$.post(evigallery_object.ajax_url, data, function(dat) {
			var tmp = $.parseJSON(dat);
			var item = tmp[0];
			var path = tmp[1];
			var field = $("#evigallery-items-ids");
			if(field.val() !== "")
				var items = $.parseJSON(field.val());
			else
				var items = [];
			if(!evigallery_searchItem(items,item))
			{
				items.push(item);
				field.val(JSON.stringify(items));
				$("#evigallery-items").append('<div class="evigallery-thumb"><img src="'+path+'" data-type="video" data-prov="'+item.prov+'" data-idatt="'+item.id_att+'" data-vid="'+item.vid+'"><a href="#" class="evigallery-close media-modal-icon" title="Delete"></a></div>');
			}
			else
			{
				$('#messageGallery').html('<div class="msg-gal msg-gal-error">'+evigallery_object.duplivid+'</div>');
			}
			$("#evigallery-items .evigallery-thumb-load").addClass("hidden");
		});
	}
	
	function evigallery_updateItems( evigallery_items )
	{
		var items = [];
		var item;
		$(evigallery_items).children('.evigallery-thumb').children('img').each(function(){
			data = $(this).data('itemid')
			if($(this).data('type') == 'image')
			{
				item = {
					'type': 'image',
					'id_att': parseInt($(this).data('idatt')),
				}
			}
			else
			{
				item = {
					'type': 'video',
					'id_att': parseInt($(this).data('idatt')),
					'prov': $(this).data('prov'),
					'vid': $(this).data('vid'),
				}
			}
			items.push( item );
		});
		$("#evigallery-items-ids").val(JSON.stringify(items));
	}

    $('#evigallery-add-file').on('click',function(e) {
        e.preventDefault();
        var custom_uploader;
        var $add_img_button = $(this);
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: evigallery_object.title,
            button: {
                text: evigallery_object.button
            },
			library : { type : 'image' },
            multiple: true,
		});
		custom_uploader.on( 'select', function() {
			var selection = custom_uploader.state().get('selection');
			selection.map( function( attachment ) {
				attachment = attachment.toJSON(); 
				evigallery_addMedia(attachment);
			}); 
		});
		custom_uploader.open();
	});


    $('#evigallery-video-url-add').on('click',function(e) {
        e.preventDefault();
		var title = $('#evigallery-video-title').val();
		var url = $('#evigallery-video-url').val();
		var videoid = url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
		if(videoid){
			evigallery_addVideo(videoid[1],title);
			tb_remove();
			$('#evigallery-video-title').val("");
			$('#evigallery-video-url').val("");
		} else
			alert("Unknown address format");
	});
	
	var evigallery_items = $('#evigallery-items');
	if( evigallery_items.length > 0 )
	{
		evigallery_items.sortable({
			update: function(event, ui){
				evigallery_updateItems( $(this) );
			}
		});
	}
	
	$('body').on('click', '#evigallery-items .evigallery-thumb a.evigallery-close', function(e){
		e.preventDefault();
		var evigallery_items = $(this).parent().parent();
		$(this).parent().remove();
		evigallery_updateItems( evigallery_items );
	});
	
	$("#evigallery-mce-btn").click(function(){
		var H = 400;
		var W = 400;
		tb_show( 'Galerie Soliles', '#TB_inline?height='+H+'&width='+W+'&inlineId=evigallery-mce-content' );
		$('#TB_window').css("height", H);
		$('#TB_window').css("width", W);	
			
		$('#TB_window').css("top", (($(window).height() - H) / 4) + 'px');
		$('#TB_window').css("left", (($(window).width() - W) / 4) + 'px');
		$('#TB_window').css("margin-top", (($(window).height() - H) / 4) + 'px');
		$('#TB_window').css("margin-left", (($(window).width() - W) / 4) + 'px');
	});
	
	$("#evigallery-viewmode-gallery").click(function(){
		$("#evigallery-form").show();
		$("#evigallery-album-form").hide();
	});
	
	$("#evigallery-viewmode-album").click(function(){
		$("#evigallery-form").hide();
		$("#evigallery-album-form").show();
	});
	
	$("#evigallery-insert-gallery").click(function(){
		var gallery = $("#evigallery-choose").val();
		var shortcode = '[evigallery view="gallery" id="'+gallery+'"]';
		if( jQuery('#wp-content-editor-container > textarea').is(':visible') ) {
			var val = jQuery('#wp-content-editor-container > textarea').val() + shortcode;
			jQuery('#wp-content-editor-container > textarea').val(val);	
		}
		else {tinyMCE.activeEditor.selection.setContent(shortcode);}
		tb_remove();
	});
	
	$("#evigallery-insert-album").click(function(){
		var album = $("#evigallery-album-choose").val();
		var shortcode = '[evigallery view="album" id="'+album+'"]';
		if( jQuery('#wp-content-editor-container > textarea').is(':visible') ) {
			var val = jQuery('#wp-content-editor-container > textarea').val() + shortcode;
			jQuery('#wp-content-editor-container > textarea').val(val);	
		}
		else {tinyMCE.activeEditor.selection.setContent(shortcode);}
		tb_remove();
	});
	
	$('#recreatethumb-btn').click(function(){
		$('#dorecreatethumb').val(1);
		$('#evigallery-form').submit();
	});
	
});