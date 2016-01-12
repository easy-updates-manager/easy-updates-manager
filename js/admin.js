jQuery( document ).ready( function( $ ) {        
    function eum_checkbox_save( $checkbox ) {
        checkbox_id = $checkbox.attr( 'id' );
         
         $.each( $checkbox, function() {
             data_context = jQuery( this ).data( 'context' );
             data_action = jQuery( this ).data( 'action' );
             data_checked = jQuery( this ).prop('checked');
             if ( data_checked ) {
                data_checked = 'on';  
             } else {
                data_checked = 'off';
             }
             data_val = jQuery( this ).val();
              
              $.post( ajaxurl, { action: 'mpsum_ajax_action', context: data_context, data_action: data_action, _ajax_nonce: $( '#_mpsum' ).val(), checked: data_checked, val: data_val }, function( response ) {
            } );
             
         } );   
    };
    function eum_toggle_main( $checkbox ) {
        if( $checkbox.is( ':checked' ) ) {
            $checkboxes_to_enable = $( ".main-updates input[type='checkbox']" );
            $.each( $checkboxes_to_enable, function( index, checkbox ) {
                checkbox = $( checkbox );
                $( checkbox ).prop( 'disabled', false );
                if ( checkbox.data( 'value' ) == 'on' ) {
                    checkbox.parent().parent().addClass( 'active' );
                    $( checkbox ).prop( 'checked', true );
                }                
            } );  
        } else {
            $( $checkbox ).prop( 'disabled', false );
            $checkboxes_to_disable = $( ".main-updates input[type='checkbox']" );
            $.each( $checkboxes_to_disable, function( index, checkbox ) {
                checkbox = $( checkbox );
                checkbox.prop( 'checked', false );
                checkbox.prop( 'disabled', true );
                checkbox.parent().parent().removeClass( 'active' );
            } );
        }
        $( $checkbox ).prop( 'disabled', false );
    };
    
    /* For when other button is clicked */
    $( '.dashboard-item' ).on( 'click', function( e ) {
        $input_wrapper = jQuery( this );
        $radio_boxes = $input_wrapper.find( 'input[type="radio"]:checked' );
        if ( $radio_boxes.length > 0 ) {
            eum_checkbox_save( $radio_boxes );
            return;
        }
        e.preventDefault();
        
        $checked_boxes = $input_wrapper.find( 'input[type="checkbox"]:checked' );
        $unchecked_boxes = $input_wrapper.find( 'input:checkbox:not(:checked)' );
        
        if ( $checked_boxes.length > 0 ) {
            if ( $checked_boxes.prop( 'disabled' ) == true ) {
                return;
            }
            $checked_boxes.data( 'value', 'off' );
            $checked_boxes.prop( 'checked', false );
            $checked_boxes.parent().parent().toggleClass( 'active' );
            if ( $checked_boxes.prop( 'id' ) == 'all_updates_off' ) {
                eum_toggle_main( $checked_boxes );   
            }
            eum_checkbox_save( $checked_boxes );
        } else if( $unchecked_boxes.length > 0 ) {
            if ( $unchecked_boxes.prop( 'disabled' ) == true ) {
                return;
            }
            $unchecked_boxes.data( 'value', 'on' );
            $unchecked_boxes.prop( 'checked', true );
            $unchecked_boxes.parent().parent().toggleClass( 'active' );
            if ( $unchecked_boxes.prop( 'id' ) == 'all_updates_off' ) {
                eum_toggle_main( $unchecked_boxes );  
            }
            eum_checkbox_save( $unchecked_boxes );
        }
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
    /* Automatic Updates */
    $( '.dashboard-plugin-theme-auto-updates' ).on( 'click', 'a', function( e ) {
        e.preventDefault();
        tag_action = jQuery( this ).attr( 'data-tab-action' );
        $( '.dashboard-plugin-theme-auto-updates  .dashboard-tab-themes' ).toggleClass( 'active' );
        $( '.dashboard-plugin-theme-auto-updates  .dashboard-tab-plugins' ).toggleClass( 'active' );
        $( '.dashboard-plugin-theme-auto-updates .dashboard-tab-header-plugin' ).toggleClass( 'active' );
        $( '.dashboard-plugin-theme-auto-updates .dashboard-tab-header-theme' ).toggleClass( 'active' );
    } ); 
    
} );