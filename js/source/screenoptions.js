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
} );