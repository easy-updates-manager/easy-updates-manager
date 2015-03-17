<?php
/**
 * Help Screen for Easy Updates Manager
 *
 * Initializes and outputs the help screen for the plugin.
 *
 * @since 5.0.0
 *
 * @package WordPress
 */
class MPSUM_Admin_Help {
	
	/**
	* Class constructor.
	*
	* Initialize the class
	*
	* @since 5.0.0
	* @access public
	*
	*/
	public function __construct() {
		$screen = get_current_screen();

		$screen  = get_current_screen();
		$content1 = '<p>';
		$content1 .= esc_html__( 'This is the Easy Updates Manager settings help tab. In here you will find helpful information on what Easy Updates Manager does and how to use it.', 'stops-core-theme-and-plugin-updates' );
		$content1 .= '</p>';
		$content1 .= sprintf( '<div><p><strong>%s - </strong>%s</p></div>', esc_html__( 'Please Note!', 'stops-core-theme-and-plugin-updates' ), esc_html__( 'If either your WordPress core, theme, or plugins get too out of date, you may run into compatibility problems. Check the capability tab for more information.', 'stops-core-theme-and-plugin-updates' ) );

		$content2 = <<<CONTENT2
<div><br /><br /><a target="_blank"><a href="https://github.com/easy-updates-manager/easy-updates-manager/wiki">Please see our Wiki for documentation and videos</a>.</div>

CONTENT2;

		$content3 = <<<CONTENT3
	<p>
        <a href="http://mpswp.wordpress.com" class="button">Our Website</a>
	    <a href="http://wordpress.org/support/plugin/stops-core-theme-and-plugin-updates" class="button">Support on WordPress</a>
	    <a href="https://github.com/easy-updates-manager/easy-updates-manager" class="button">GitHub Repository</a>
	     <a href="https://github.com/easy-updates-manager/easy-updates-manager/wiki" class="button">Official Documentation</a>
    </p>

CONTENT3;

		$content4 = <<<CONTENT4
<p>
You'll see four tabs where you can configure the update options.
<br>
<br>
<strong>General</strong> - Use this screen to finely tune which updates and automatic updates you would like to see.
<br>
<br>
<strong>Plugins</strong> - If plugin updates are enabled and/or automatic updates for plugins are enabled, you can configure which plugins will receive updates and/or automatic updates.
<br>
<br>
<strong>Themes</strong> - If theme updates are enabled and/or automatic updates for themes are enabled, you can configure which themes will receive updates and/or automatic updates.
<br>
<br>
<strong>Advanced</strong> - Reset all options or allow certain users to see all updates regardless of what settings you have set.
<br>
<br>
</p>

CONTENT4;

		$content5 = <<<CONTENT5
		
<p style="align: center;">
<h3>Contributors:</h3>
<ul>
<li><a href="http://profiles.wordpress.org/kidsguide/">Matthew</a></li>
<li><a href="http://profiles.wordpress.org/mps-plugins/">MPS Plugins</a></li>
<li><a href="http://profiles.wordpress.org/ronalfy/">Ronalfy</a></li>
<li><a href="http://profiles.wordpress.org/shazahm1hotmailcom/">Shazahm1</a></li>
<li><a href="http://profiles.wordpress.org/szepeviktor/">szepe.viktor</a></li>
</ul>
</p>

CONTENT5;

		$content6 = <<<CONTENT6
<p>	
WordPress encourages you to update your plugins, themes, and core to make sure that there are no bugs. Even though you most likely want to disable all the updates and never think about updating again, you should still consider updating every once in a while to avoid major bugs and errors on your WordPress website.

<h3>This plugin is tested so there are no problems.</h3>
<ul>
<li>Tested with WordPress 4.1.1.</li>
<li>Tested with popular plugins to ensure that there are no conflicts.</li>
<li>Tested with popular themes to ensure that there are no conflicts.</li>
</ul>
</p>

CONTENT6;

		$screen->add_help_tab(array(
				'id'      => 'help_tab_content_1',
				'title'   => __('Overview'),
				'content' => $content1,
			));
			
	    $screen->add_help_tab(array(
                'id' => 'help_tab_content_4',
                'title' => __('Tabs'),
                'content' => $content4,
            ));
			
	    $screen->add_help_tab(array(
                'id' => 'help_tab_content_2',
                'title' => __('Documentation'),
                'content' => $content2,
            ));	
			
	    $screen->add_help_tab(array(
                'id' => 'help_tab_content_6',
                'title' => __('Capability'),
                'content' => $content6,
            ));
			
	    $screen->add_help_tab(array(
                'id' => 'help_tab_content_3',
                'title' => __('Troubleshooting'),
                'content' => $content3,
            ));
				
		$screen->set_help_sidebar($content5);
	
	} //end constructor
	
}