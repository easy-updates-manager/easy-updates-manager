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
    
    swal({
  html:
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
    '</button>',
    type: 'question',
  showCloseButton: true,
  showCancelButton: true,
  confirmButtonText:
    '<i class="fa fa-thumbs-up"></i> I know what I\'m Doing!'
})

	jQuery( 'body' ).on( 'click', '#eum-configure-manually', function( e ) {
		e.preventDefault();
		swal.close()
	} );
	
	
	jQuery( 'body' ).on( 'click', '#eum-disable-manually', function( e ) {
		e.preventDefault();
		swal.close();
	} );

} );