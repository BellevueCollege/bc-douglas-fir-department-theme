jQuery(document).ready(function($) {
	$('#sortable-table tbody').sortable({
		axis: 'y',
		handle: '.column-order img',
		placeholder: 'ui-state-highlight',
		forcePlaceholderSize: true,
		update: function(event, ui) {
			var theOrder = $(this).sortable('toArray');

			// Nonce is localized from PHP; WordPress checks it in the AJAX handler.
			var data = {
				action: 'bc_douglas_fir_cpt_update_post_order',
				postType: $(this).attr('data-post-type'),
				order: theOrder,
				_ajax_nonce: bcDouglasFirPostOrder.nonce
			};
			$.post(ajaxurl, data);
		}
	}).disableSelection();
});
