jQuery( document ).ready( function( $ ) {    
    $( '.dashboard-item-choice' ).on( 'change', '#all_updates_on', function( e ) {
       input_var = 'on';
       console.log( $( this ).attr( 'id'  ));
       if ( 'checked' == $( this ).attr( 'checked' ) ) {
           input_var = 'on';
        } else {
            input_var = 'off';   
        }
        $.post( ajaxurl, { action: 'mpsum_disable_updates', new_val: input_var }, function( response ) {
            if ( response.length > 0 ) {
                 $.each( response, function( key, value ) { 
                     $input_checkbox = $( '#' + value );
                    if ( 'checked' == $input_checkbox.attr( 'checked' ) ) {
                        $input_checkbox.removeAttr( 'checked' );
                    }
                 } );
            } else {
                $.each( jQuery( 'input.update-option' ), function() {
                    $element = jQuery( this );
                    id = $element.attr( 'id' ) + '_before';
                    is_checked = jQuery( '#' + id ).val();
                    if ( '' == is_checked ) {
                        $element.removeAttr( 'checked' );
                        return;
                    }
                    $element.attr( 'checked', jQuery( '#' + id ).val() );
                } );
            }
            
        }, 'json');
    } );
    
} );