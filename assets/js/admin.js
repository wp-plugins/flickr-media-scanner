jQuery(document).ready(function() {

	flickr_images = new Array();
	flickr_index = 0;

	jQuery('.fms-image-in-post').each(function(element) {
		var flickr_image = jQuery(this).attr('data-image');
		flickr_images.push(flickr_image);
	});

	if(flickr_images.length > 0 && fms_api_key != '') {
		doAjaxCall();
	}

});

function doAjaxCall() {
    jQuery.ajax({
        url: 'https://api.flickr.com/services/rest/?format=json&method=flickr.photos.getInfo&api_key=' + fms_api_key,
        dataType: 'jsonp',
        data: {'photo_id': flickr_images[flickr_index]},
        type: 'GET',
        jsonpCallback: 'jsonFlickrApi',
        success: function(data){}
    });	
}

function jsonFlickrApi(data) {
	if(data.stat == 'ok') {
		jQuery('#fms-image-' + flickr_images[flickr_index]).removeClass('fms-loading').addClass('fms-ok');
	} else {
		jQuery('#fms-image-' + flickr_images[flickr_index]).removeClass('fms-loading').addClass('fms-fail');
	}
	flickr_index++;
	if(flickr_index < flickr_images.length) {
		doAjaxCall();
	}
}
