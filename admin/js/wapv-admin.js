(function( $ ) {
	'use strict';
	$(document).on('change', '.wapv-visibility .wapv-visibility-checkbox', function (e) {
		$.ajax({
			url: wapv_object.ajax_url,
			type: "POST",
			data: {
				action: 'wapv_product_visibility',
				nonce: $(this).data('nonce'),
				product_id: $(this).data('id')
			},
			success: function (response) {
				//console.log(response);
				if (response.success) {
					//show the toastr js message
				} else {
					//show the toastr js message
				}
			}
		});
	});

})( jQuery );
