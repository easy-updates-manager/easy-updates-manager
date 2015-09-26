jQuery( document ).ready( function( $ ) {    
    $( '.dashboard-item-choice' ).on( 'change', 'input', function( e ) {
       input_var = 'on';
       if ( 'checked' == $( this ).attr( 'checked' ) ) {
           input_var = 'on';
        } else {
            input_var = 'off';   
        }
        $.post( ajaxurl, { action: 'mpsum_disable_updates', new_val: input_var }, function( response ) {
            $.each( response, function( key, value ) {
                if( 'off' == input_var ) {
                    $input_checkbox = $( '#' + value );
                    if ( 'checked' == $input_checkbox.attr( 'checked' ) ) {
                        $input_checkbox.removeAttr( 'checked' );
                    }
                } else {
                    $input_checkbox = $( '#' + value );
                    $input_checkbox.attr( 'checked', 'checked' );
                }
            } );
        }, 'json');
    } );
    
} );