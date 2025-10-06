jQuery(document).ready(function() {
	jQuery('.menu-item').has('.sub-menu').each( function() {
		if ( jQuery( this ).hasClass('active') ) {
			var linkText = jQuery( this ).children('a').first().text();
			jQuery( this ).prepend( collapseLink( linkText ) );
		} else {
			jQuery( this ).prepend( expandLink( linkText ) );
		}
	});
});

jQuery('.sidebar').on('click', '.menu-expand-button', function( event ) {
	event.preventDefault();
	var linkText = jQuery( this ).siblings('a').not('.menu-expand-button').first().text();
	if ( jQuery( this ).hasClass('expanded') ){
		jQuery(this).siblings('.sub-menu').slideUp( 100 );
		jQuery(this).replaceWith( expandLink( linkText ) );
	} else {
		jQuery(this).siblings('.sub-menu').slideDown( 100 );
		jQuery(this).replaceWith( collapseLink( linkText ) );
	}
});

function expandLink( linkText ) {
	return '<a class="menu-expand-button" href="#" aria-expanded="false"><i class="fa-solid fa-chevron-down"></i><span class="sr-only">Expand ' + linkText + ' Submenu</span></a>';
}
function collapseLink( linkText ) {
	return '<a class="menu-expand-button expanded" href="#" aria-expanded="true"><i class="fa-solid fa-minus"></i><span class="sr-only">Collapse ' + linkText + ' Submenu</span></a';
}
