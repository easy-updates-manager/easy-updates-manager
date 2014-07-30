;jQuery(document).ready( function($) {

	/*
	 * Add jQuery Chosen to enhance selects.
	 */
	if ( $.fn.chosen ) {

		$('.dum-enhanced-select').chosen( { width: '100%' } );

		$('#dum-disable-themes, #dum-disable-plugins').click( function() {

			id = $(this).attr('id');

			if ( $(this).is(':checked') ) {

				$( '#' + id + '-select').attr( 'disabled', false ).trigger('chosen:updated');

			} else {

				$( '#' + id + '-select').val( function( i, value ) { return $(this).data('placeholder'); } ).attr( 'disabled', true ).trigger('chosen:updated');
			}

		});
	}

});
