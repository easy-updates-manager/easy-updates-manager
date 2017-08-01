jQuery( document ).ready( function( $ ) {
	/* Screen Options */
    var dashboard_checked = '';
    if ( 'on' == mpsum.dashboard_showing ) {
	    dashboard_checked = ' checked="checked"';
    }
    var screen_options_html = '<fieldset class="screen-options">';
    screen_options_html += '<legend>' + mpsum.tabs + '</legend>';
    screen_options_html += '<input type="hidden" value="off" name="mpsum_dashboard" />';
    screen_options_html += '<input type="checkbox" id="mpsum_dashboard" value="on" name="mpsum_dashboard"' + dashboard_checked + '/>';
    screen_options_html += '&nbsp;<label for="mpsum_dashboard">' + mpsum.dashboard + '</label>';
    screen_options_html += '</fieldset>';
    $( '#screen-options-wrap #adv-settings' ).prepend( screen_options_html );
    
    if ( mpsum.new_user == 'on' ) {
	    swal({
		  html:
		  	'<div id="mpsum-welcome-modal">' +
		    '<h2>' + mpsum.welcome + '</h2>, ' +
		    '<h3>' + mpsum.welcome_intro + '</h3>' + 
		    '<button id="eum-enable-autoupdates" class="eum-button button button-primary" name="eum_enable_automatic" value="on" id="eum_type_1">' +
		    mpsum.welcome_automatic +
		    '</button>' +
		    '<button id="eum-disable-manually" class="eum-button button button-primary" name="eum_type_disable_updates" value="on" id="eum_type_2">' +
		    mpsum.welcome_disable +
		    '</button>' +
		    '<button id="eum-configure-manually" class="eum-button button button-primary" name="eum_enable_automatic" value="on" id="eum_type_1">' +
		    'Configure Manually' +
		    '</button>' +
		    '</div>',
		    type: 'question',
		  showCloseButton: true,
		});
    }
    

	jQuery( 'body' ).on( 'click', '#eum-configure-manually', function( e ) {
		e.preventDefault();
		jQuery( '#mpsum-welcome-modal' ).html();
		jQuery.post( ajaxurl, {action: 'mpsum_ajax_remove_wizard', _ajax_nonce: mpsum.admin_nonce}, function( response ) {
			window.top.location.reload();
		} );
	} );
	
	
	jQuery( 'body' ).on( 'click', '#eum-disable-manually', function( e ) {
		e.preventDefault();
		jQuery( '#mpsum-welcome-modal' ).html();
		jQuery.post( ajaxurl, {action: 'mpsum_ajax_disable_updates', _ajax_nonce: mpsum.admin_nonce}, function( response ) {
			//swal.close();
			window.top.location.reload();
		} );
	} );
	
	jQuery( 'body' ).on( 'click', '#eum-enable-autoupdates', function( e ) {
		e.preventDefault();
		jQuery( '#mpsum-welcome-modal' ).html();
		jQuery.post( ajaxurl, {action: 'mpsum_ajax_enable_automatic_updates', _ajax_nonce: mpsum.admin_nonce}, function( response ) {
			//swal.close();
			window.top.location.reload();
		} );
	} )

} );