jQuery(document).ready(function ($) {
	$( "#main img:not([alt])" ).each( function() {
		this.setAttribute('data-toggle', 'tooltip');
		this.setAttribute('data-placement', 'auto top');
		this.setAttribute('title', '⚠️ Warning: This image does not have an Alternative Text (ALT) attribute. This is not accessible.');
	});

	$( '#main img[alt=""]' ).each(function () {
		if (!$(this).hasClass('a11y-decorative')) {
			console.log('Adding tooltip to image with empty alt attribute');
			this.setAttribute('data-toggle', 'tooltip');
			this.setAttribute('data-placement', 'auto top');
			this.setAttribute('title', '⚠️ Notice: There is no Alternative Text provided for this image. This is only allowed if this image is purely decorative.');
		}
	});

	$('#main a > img[alt=""]').each(function () {
		this.setAttribute('title', '⚠️ Warning: There is no Alternative Text provided for this image link. This is not accessible.');
	});
	$('[data-toggle="tooltip"]').tooltip();

});