jQuery(document).ready(function($) {

	$('.evigallery-container a').each(function(){
		var id = $(this).attr("id");
		$('a.gal-'+id).colorbox({ inline:true, rel:'gal-'+id, innerWidth:evigallery_getWidth, innerHeight:evigallery_getHeight, opacity:".7", scrolling: false});
	});
	
	if($('.evigallery-items').length)
		var evigallery_thumbHeight = parseInt($('.evigallery-items').css('height').replace('px',''));
	
	$('.evigallery-container a img').each(function(){
		if(parseInt($(this).attr('height')) < evigallery_thumbHeight)
		{
			$(this).css('height','100%');
			$(this).css('width','auto');
			$(this).css('maxWidth','none');
		}
	});
	
	function evigallery_getWH(elt,ret)
	{
		if(elt.hasClass("evigallery-video-iframe"))
		{
			var w = 640;
			var h = 480;
		}else{
			var w = parseInt(elt.attr("width"));
			var h = parseInt(elt.attr("height"));
		}
		var W = Math.floor(85 * $('body').width()/ 100);
		var H = Math.floor(85 * $(window).height()/ 100);
		var rw = Math.min(w,W-120);
		var rh = Math.floor(rw * h / w );
		if( rh > H)
		{
			rh = Math.min(h,H);
			rw = Math.floor(rh * w / h);
		}
		if(elt.hasClass("evigallery-video-iframe"))
		{
			elt.attr("width",rw);
			elt.attr("height",rh);
		}
		if(ret=="w")
			return rw;
		else
			return rh;
	}
	function evigallery_getElt()
	{
		var elt = $($.colorbox.element().attr("href")+" img")
		if( !elt.length )
			elt = $($.colorbox.element().attr("href")+" iframe")
		return elt
	}
	function evigallery_getWidth() {
		var elt = evigallery_getElt();
		return ( elt.length )? evigallery_getWH(elt,"w") : "";
	}
	function evigallery_getHeight() {
		var elt = evigallery_getElt();
		return ( elt.length )? evigallery_getWH(elt,"h") : "";
	}
	$(window).resize(function(){
		$.colorbox.resize({innerWidth:evigallery_getWidth(), innerHeight:evigallery_getHeight()});
	});
});