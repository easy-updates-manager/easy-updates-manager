jQuery( document ).ready( function( $ ) {    
    /* When all updates button is clicked */
    $( '.dashboard-item-choice' ).on( 'change', '#all_updates_on', function( e ) {
       input_var = 'on';
       if ( 'checked' == $( this ).attr( 'checked' ) ) {
           input_var = 'on';
        } else {
           // $( this ).parent().parent().toggleClass( 'active' );
            input_var = 'off';   
        }
        $.post( ajaxurl, { action: 'mpsum_disable_updates', new_val: input_var, _ajax_nonce: $( '#_mpsum' ).val() }, function( response ) {
           //todo - Fix JS tabs
           /* if ( response.length > 0 ) {
                 $.each( response, function( key, value ) { 
                     $input_checkbox = $( '#' + value );
                    if ( 'checked' == $input_checkbox.attr( 'checked' ) ) {
                        $input_checkbox.removeAttr( 'checked' );
                        $input_checkbox.parent().parent().toggleClass( 'active' );
                    }
                 } );
            } else {
                $.each( jQuery( 'input.update-option' ), function() {
                    $element = jQuery( this );
                    $before = $element.siblings(':first' )
                    is_checked = $before.val();
                    if ( '' == is_checked ) {
                        $element.removeAttr( 'checked' );
                         $element.parent().parent().toggleClass( 'active' );
                        return;
                    }
                    $element.attr( 'checked', $before.val() );
                } );
            }*/
            
        }, 'json');
    } );
    
    /* For when other button is clicked */
    $( '.dashboard-item' ).on( 'click', function( e ) {
        $input_wrapper = jQuery( this );
        $checked_boxes = $input_wrapper.find( 'input[type="checkbox"]:checked' );
        $unchecked_boxes = $input_wrapper.find( 'input:checkbox:not(:checked)' );
        
        if ( $checked_boxes.length > 0 ) {
            $checked_boxes.prop( 'checked', false );
            $checked_boxes.parent().parent().toggleClass( 'active' );
        } else if( $unchecked_boxes.length > 0 ) {
            $unchecked_boxes.prop( 'checked', true );
            $unchecked_boxes.parent().parent().toggleClass( 'active' );
        }
    } );
    return;
    $( ".dashboard-item" ).on( 'change', 'input', function( e ) {
         $checkbox = jQuery( this );
         $checkbox.parent().parent().toggleClass( 'active' );
         checkbox_id = $checkbox.attr( 'id' );
         if ( checkbox_id == 'all_updates_on' ) {
            return;    
         }  
         
         $.each( $checkbox, function() {
             data_context = jQuery( this ).data( 'context' );
             data_action = jQuery( this ).data( 'action' );
             data_checked = jQuery( this ).attr( 'checked' );
             data_val = jQuery( this ).val();
             if ( data_checked == '' || undefined == data_checked ) {
                 data_checked = 'off';
              } else {
                data_checked = "on";  
              }
              
              $.post( ajaxurl, { action: 'mpsum_ajax_action', context: data_context, data_action: data_action, _ajax_nonce: $( '#_mpsum' ).val(), checked: data_checked, val: data_val }, function( response ) {
            } );
             
         } );
     } );
    
    
    /* Plugin / Theme Tabs */
    $( '.dashboard-plugin-theme-updates' ).on( 'click', 'a', function( e ) {
        e.preventDefault();
        tag_action = jQuery( this ).attr( 'data-tab-action' );
        $( '.dashboard-plugin-theme-updates .dashboard-tab-themes' ).toggleClass( 'active' );
        $( '.dashboard-plugin-theme-updates .dashboard-tab-plugins' ).toggleClass( 'active' );
        $( '.dashboard-plugin-theme-updates .dashboard-tab-header-plugin' ).toggleClass( 'active' );
        $( '.dashboard-plugin-theme-updates .dashboard-tab-header-theme' ).toggleClass( 'active' );
    } );
    /* Plugin / Theme Tabs */
    $( '.dashboard-plugin-theme-auto-updates' ).on( 'click', 'a', function( e ) {
        e.preventDefault();
        tag_action = jQuery( this ).attr( 'data-tab-action' );
        $( '.dashboard-plugin-theme-auto-updates  .dashboard-tab-themes' ).toggleClass( 'active' );
        $( '.dashboard-plugin-theme-auto-updates  .dashboard-tab-plugins' ).toggleClass( 'active' );
        $( '.dashboard-plugin-theme-auto-updates .dashboard-tab-header-plugin' ).toggleClass( 'active' );
        $( '.dashboard-plugin-theme-auto-updates .dashboard-tab-header-theme' ).toggleClass( 'active' );
    } ); 
    
} );