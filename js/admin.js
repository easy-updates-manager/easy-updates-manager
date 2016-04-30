jQuery( document ).ready( function( $ ) {        
    function eum_checkbox_save( $checkbox ) {
        checkbox_id = $checkbox.attr( 'id' );
         
         $.each( $checkbox, function() {
             var checkbox = jQuery( this );
             data_context = checkbox.data( 'context' );
             data_action = checkbox.data( 'action' );
             data_checked = checkbox.prop('checked');
             if ( data_checked ) {
                data_checked = 'on';  
             } else {
                data_checked = 'off';
             }
             data_val = checkbox.val();
             if ( checkbox.parent().hasClass( 'multi-choice' ) ) {
                 checkbox.parent( '.multi-choice' ).hide();
                 checkbox.parent().parent().append( '<div class="mpsum-spinner" id="spinner-' + checkbox.prop( 'id' ) + '"><img src="' + mpsum.spinner + '"></div>' );
             } else {
                 checkbox.siblings( 'label' ).hide();
                 checkbox.parent().append( '<div class="mpsum-spinner" id="spinner-' + checkbox.prop( 'id' ) + '"><img src="' + mpsum.spinner + '"></div>' );
             }
             
             
              
             $.post( ajaxurl, { action: 'mpsum_ajax_action', context: data_context, data_action: data_action, _ajax_nonce: $( '#_mpsum' ).val(), checked: data_checked, val: data_val }, function( response ) {
                     if ( checkbox.parent().hasClass( 'multi-choice' ) ) {
                         checkbox.parent().parent().find( '.mpsum-spinner' ).remove();
                         checkbox.parent( '.multi-choice' ).show();
                     } else {
                         checkbox.parent().find( '.mpsum-spinner' ).remove();
                         checkbox.siblings( 'label' ).show();
                     }
                  
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
    $( '.dashboard-item' ).on( 'change', function( e ) {
        $input_wrapper = jQuery( this );
        $radio_boxes = $input_wrapper.find( 'input[type="radio"]:checked' );
        if ( $radio_boxes.length > 0 ) {
            eum_checkbox_save( $radio_boxes );
            return;
        }
        e.preventDefault();
        
        $checked_boxes = $input_wrapper.find( 'input[type="checkbox"]:checked' );
        $unchecked_boxes = $input_wrapper.find( 'input:checkbox:not(:checked)' );
        
        if ( $unchecked_boxes.length > 0 ) {
            if ( $unchecked_boxes.prop( 'disabled' ) == true ) {
                return;
            }
            $unchecked_boxes.attr( 'value', 'off' );
            $unchecked_boxes.prop( 'checked', false );
            $unchecked_boxes.parent().parent().toggleClass( 'active' );
            if ( $unchecked_boxes.prop( 'id' ) == 'all_updates_off' ) {
                eum_toggle_main( $unchecked_boxes );   
            }
            eum_checkbox_save( $unchecked_boxes );
        } else if( $checked_boxes.length > 0 ) {
            if ( $checked_boxes.prop( 'disabled' ) == true ) {
                return;
            }
            $checked_boxes.attr( 'value', 'on' );
            $checked_boxes.prop( 'checked', true );
            $checked_boxes.parent().parent().toggleClass( 'active' );
            if ( $checked_boxes.prop( 'id' ) == 'all_updates_off' ) {
                eum_toggle_main( $checked_boxes );  
            }
            eum_checkbox_save( $checked_boxes );
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