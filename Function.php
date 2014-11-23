<?php
 /*
 * @package Disable Updates Manager
 * @author MPS Plugins
 * @email mpsplugins@gmail.com
 * @version 4.6.0
 */
/*
Plugin Name: Disable Updates Manager
Plugin URI: http://www.mpswp.wordpress.com
Version: 4.6.0
Description: A configurable plugin that disables updates for you. Easy, clean and helpful.
Author: MPS Plugins
Author URI: http://www.mpswp.wordpress.com
Author Email: mpsplugins@gmail.com
License: GPL2
Text Domain: disable-updates-manager
Domain Path: languages

@Copyright 2013 - 2014 MPS Plugins (email: mpsplugins@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

Go to the license.txt in the trunk for more information.
*/

class Disable_Updates {

	// Define version.
	const VERSION = '4.6.0';

	private static $page_hook = '';

	function __construct() {

		// Load our textdomain
		add_action( 'init', array( __CLASS__ , 'load_textdomain' ) );

		// Add menu page.
		add_action( 'admin_menu', array( __CLASS__, 'add_submenu' ) );

		// Settings API.
		add_action( 'admin_init', array( __CLASS__, 'register_setting' ) );

		// Add action links.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( __CLASS__, 'action_links' ) );

		// Add meta links.
		add_filter( 'plugin_row_meta', array( __CLASS__, 'meta_links' ), 10, 2 );

		// load the values recorded.
		$this->load_disable_updates();
	}
			
	static function load_textdomain() {
	
		load_plugin_textdomain( 'disable-updates-manager', FALSE, basename( dirname( __FILE__ ) ) . '/lang' );
	
	}

	static function page_actions() {

		do_action( 'add_meta_boxes_' . self::$page_hook, NULL );
		do_action( 'add_meta_boxes', self::$page_hook, NULL );

		// User can choose between 1 or 2 columns (default 2)
		add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );

		add_meta_box( 'dum-global', __( 'Disable Updates Globally', 'disable-updates-manager' ), array( __CLASS__, 'metabox_global' ), self::$page_hook, 'left', 'core' );
        add_meta_box( 'dum-themes', __( 'Disable Theme Updates', 'disable-updates-manager' ), array( __CLASS__, 'metabox_themes' ), self::$page_hook, 'right', 'core' );
		
		add_meta_box( 'dum-other', __( 'Other Settings', 'disable-updates-manager' ), array( __CLASS__, 'metabox_other' ), self::$page_hook, 'left', 'core' );
		add_meta_box( 'dum-plugins', __( 'Disable Plugin Updates', 'disable-updates-manager' ), array( __CLASS__, 'metabox_plugins' ), self::$page_hook, 'right', 'core' );
	}

	static function enqueue_css() {

		// If SCRIPT_DEBUG is set and TRUE load the non-minified files, otherwise, load the minified files.
		$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'dum-chosen', plugins_url( "vendor/chosen/chosen$min.css", __FILE__ ), array(), '1.1.0' );

		wp_enqueue_style( 'disable-updates-manager', plugins_url( "assets/style$min.css", __FILE__ ), array( 'dum-chosen' ), self::VERSION );
	}

	static function enqueue_js() {

		// If SCRIPT_DEBUG is set and TRUE load the non-minified files, otherwise, load the minified files.
		$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'dum-admin-js', plugins_url( "assets/admin$min.js", __FILE__ ), array( 'postbox' ), self::VERSION );

		wp_enqueue_script( 'dum-chosen-js', plugins_url( "vendor/chosen/chosen.jquery$min.js", __FILE__ ), array( 'dum-admin-js' ), '1.1.0' );
	}
	
	static function footer_scripts() {

		?>
		<script> postboxes.add_postbox_toggles(pagenow);</script>
		<?php
	}

	// Register settings.
	static function register_setting() {

		register_setting( '_disable_updates', '_disable_updates', array( __CLASS__, 'validate_settings' ) );
	}

static function validate_settings( $value ) {


		if ( isset( $value['plugins'] ) ) {


			// Since the blocked plugins are stored in a different option, we need to update that option.
			$blocked_plugins = $value['plugins'];


			// Convert the data to match the way the options are stored.
			$blocked_plugins = array_fill_keys( $blocked_plugins, TRUE );


		} else {


			$blocked_plugins = array();
		}


		// Update the blocked plugins option.
		update_option( 'disable_updates_blocked', $blocked_plugins );


		return $value;
	}

	static function add_submenu() {

		$page_hook = add_submenu_page( 'options-general.php', 'Disable Updates Manager', __( 'Disable Updates Manager', 'disable-updates-manager' ), 'manage_options', 'disable-updates-manager', array( __CLASS__, 'display_page' ) );

		// Enqueue the admin CSS for this page only..
		add_action( "load-$page_hook", array( __CLASS__, 'enqueue_css' ) );

		// Enqueue the admin JS for this page only..
		add_action( "load-$page_hook", array( __CLASS__, 'enqueue_js' ) );

		// Add callbacks for this page only.
		add_action( "load-$page_hook", array( __CLASS__, 'page_actions' ), 9 );
		add_action( "admin_footer-$page_hook" , array( __CLASS__ , 'footer_scripts' ) );
		add_action( "load-$page_hook", array( __CLASS__, 'help_tab' ) );

		self::$page_hook = $page_hook;
	}

	static function action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . add_query_arg( array( 'page' => 'disable-updates-manager' ), admin_url( 'options-general.php' ) ) . '">' . __( 'Configure', 'disable-updates-manager' ) . '</a>',
				),
			$links
		);

	}

	static function meta_links( $links, $file ) {

		$plugin = plugin_basename( __FILE__ );

		if ( $file == $plugin ) {

			return array_merge(
				$links,
				array( '<a href="http://www.wordpress.org/support/plugin/stops-core-theme-and-plugin-updates">Support</a>' ),
				array( '<a href="https://github.com/Websiteguy/disable-updates-manager">GitHub</a>' )
			);
		}

		return $links;
	}

	// Functions for plugin (Change in settings)
	static function load_disable_updates() {

		$status = get_option( '_disable_updates' );

		if ( ! $status ) {
			return;
		}

		foreach ( $status as $id => $value ) {

			switch ( $id ) {

				// Disable Plugin Updates
				case 'plugin' :

					self::disable_plugin_updates();
					break;

				// Disable Theme Updates
				case 'theme' :

					self::disable_theme_updates();

					break;

				// Disable WordPress Core Updates
				case 'core' :

					self::disable_core_updates();

					break;

				// Remove the Dashboard Updates Menu
				case 'page' :

					// Remove the Dashboard Updates Menu Code
					add_action( 'admin_init', create_function( '', 'remove_submenu_page( \'index.php\', \'update-core.php\' );' ) );

					break;

				// Disable All Updates
				case 'all' :

					// Disable Plugin Updates Only
					self::disable_plugin_updates();

					// Disable Theme Updates Only
					self::disable_theme_updates();

					// Disable Core Updates Only
					self::disable_core_updates();

					// Disable Debug E-mails
					add_filter( 'automatic_updates_send_debug_email ', '__return_false', 1 );

					// Disable WordPress Automatic Updates
					define( 'AUTOMATIC_UPDATER_DISABLED', TRUE );
					define( 'WP_AUTO_UPDATE_CORE', FALSE );

					break;

				// Remove WordPress Version Number
				case 'wpv' :

					add_filter( 'update_footer', '__return_empty_string', 11 );

					break;

				case 'ip' :

					if ( defined( 'disable_updates_loaded' ) ) {
						return;
					}

					define( 'disable_updates_loaded', 1 );

					add_action( 'init', array( __CLASS__, 'update_plugin_block_status' ) );
					add_filter( 'site_transient_update_plugins', array( __CLASS__, 'remove_plugin_update_notification' ) );
					add_filter( 'plugin_action_links', array( __CLASS__, 'plugin_block_action_link' ), 10, 4 );

					// remove blocked plugins from being checked for updates at wordpress.org
					add_filter( 'http_request_args', array( __CLASS__, 'http_request_args_plugins_filter' ), 5, 2 );

					break;
					
				case 'bnag' :

                    if ( ! function_exists( 'c2c_no_browser_nag' ) ) :

	                function c2c_no_browser_nag() {
		            // This is cribbed from wp_check_browser_version()
		            $key = md5( $_SERVER['HTTP_USER_AGENT'] );
		            add_filter( 'site_transient_browser_' . $key, '__return_null' );
	                }
                    endif;
                    add_action( 'admin_init', 'c2c_no_browser_nag' );
					
					break;

				case 'it':

					add_filter( 'site_transient_update_themes', array( __CLASS__, 'remove_theme_update_notification' ) );

					// remove blocked themes from being checked for updates at wordpress.org
					add_filter( 'http_request_args', array( __CLASS__, 'http_request_args_themes_filter' ), 5, 2 );

					break;

				// Disable automatic background updates.
				case 'abup' :

					wp_clear_scheduled_hook( 'wp_maybe_auto_update' );
					
					break;
					
				/* 
				Version Added: 4.4.0
				Last Version Edited: 4.5.0
				Description: Disables minor core updates in the Disable Updates Manager settings.
				*/
				case 'minor-core-updates' :
				
				    //TO BE ADDED
				
				    break;
					
				/* 
				Version Added: 4.4.0
				Last Version Edited: 4.5.0
				Description: Disables major core updates in the Disable Updates Manager settings.
				*/
				case 'major-core-updates' :
				
				    //TO BE ADDED
				
				    break;
					
	            /* 
				Version Added: 4.4.0
				Description: Disables auto translation updates in the Disable Updates Manager settings.
				*/
				case 'auto-translation-updates' :
				
				    add_filter( 'auto_update_translation', '__return_false' );
				
				    break;
					
			    /* 
				Version Added: 4.4.0
				Description: Disables auto core e-mails in the Disable Updates Manager settings.
				*/
				case 'auto-core-emails' :
				
				    add_filter( 'auto_core_update_send_email', '__return_false' );
				
				    break;

			}
		}
	}

	static function update_plugin_block_status() {

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		// see if there are actions to process
		if ( ! isset( $_GET[ 'disable_updates' ] ) || ! isset( $_GET[ '_wpnonce' ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_GET[ '_wpnonce' ], 'disable_updates' ) ) {
			return;
		}

		$blocked = get_option( 'disable_updates_blocked' );
		// $plugins = get_site_transient( 'update_plugins' );

		// block action
		// if ( isset( $_GET[ 'block' ] ) && isset( $plugins->response ) && isset( $plugins->response[ $_GET[ 'block' ] ] ) ) {
		// 	$p                           = $plugins->response[ $_GET[ 'block' ] ];
		// 	$blocked[ $_GET[ 'block' ] ] = array( 'slug' => $p->slug, 'new_version' => $p->new_version );
		// }
		if ( isset( $_GET[ 'block' ] ) ) {

			$blocked[ $_GET[ 'block' ] ] = TRUE;
		}

		if ( isset( $_GET[ 'unblock' ] ) ) {

			unset( $blocked[ $_GET[ 'unblock' ] ] );
		}

		update_option( 'disable_updates_blocked', $blocked );
	}

	static function remove_plugin_update_notification( $plugins ) {

		if ( ! isset( $plugins->response ) || count( $plugins->response ) == 0 ) {

			return $plugins;
		}

		$blocked = (array) get_option( 'disable_updates_blocked' );

		// foreach ( $blocked as $filename => $plugin ) {

		// 	if ( isset( $plugins->response[ $filename ] )
		// 		 && $plugins->response[ $filename ]->new_version == $plugin[ 'new_version' ]
		// 	) {

		// 		$plugins->disable_updates[ $filename ] = $plugins->response[ $filename ];
		// 		unset( $plugins->response[ $filename ] );
		// 	}
		// }
		foreach ( $blocked as $filename => $plugin ) {

			if ( isset( $plugins->response[ $filename ] ) && $plugin == TRUE ) {

				$plugins->disable_updates[ $filename ] = $plugins->response[ $filename ];
				unset( $plugins->response[ $filename ] );
			}
		}

		return $plugins;
	}

	static function remove_theme_update_notification( $themes ) {

		$options = get_option( '_disable_updates' );

		if ( FALSE === $options ) {

			return $themes;
		}

		if ( isset( $options['themes'] ) ) {

			$blocked = $options['themes'];

		} else {

			return $themes;
		}

		if ( 0 === (int) count( $blocked ) ) {

			return $themes;
		}

		if ( ! isset( $themes->response ) || count( $themes->response ) == 0 ) {

			return $themes;
		}

		foreach ( $blocked as $theme ) {

			if ( isset( $themes->response[ $theme ] ) ) {

				$themes->disable_updates[ $theme ] = $themes->response[ $theme ];
				unset( $themes->response[ $theme ] );
			}
		}

		return $themes;
	}

	static function last_checked() {
		global $wp_version;

		return (object) array(
			'last_checked'    => time(),
			'updates'         => array(),
			'version_checked' => $wp_version,
		);
	}

	// Disable Core Updates
	static function disable_core_updates() {

		# 2.3 to 2.7:
		add_action( 'init', create_function( '', 'remove_action( \'init\', \'wp_version_check\' );' ), 2 );
		add_filter( 'pre_option_update_core', '__return_null' );

		# 2.8 to 3.0:
		remove_action( 'wp_version_check', 'wp_version_check' );
		remove_action( 'admin_init', '_maybe_update_core' );
		add_filter( 'pre_transient_update_core', array( __CLASS__,'last_checked' ) );

		# >3.0:
		remove_action( 'load-update-core.php', 'wp_update_core' );
		add_filter( 'pre_site_transient_update_core', array( __CLASS__,'last_checked' ) );

		// Hide Update Notices in Admin Dashboard
		add_action( 'admin_menu', create_function( '', 'remove_action( \'admin_notices\', \'update_nag\', 3 );' ) );

		wp_clear_scheduled_hook( 'wp_version_check' );

		// Disable email.
		add_filter( 'auto_core_update_send_email', '__return_false' );

		// This doesn't make sense. Purpose?
		// apply_filters( 'automatic_core_updates_send_debug_email', TRUE, $type, $core_update, $result );
	}

	// Disable Plugin Updates
	static function disable_plugin_updates() {

		# 2.3 to 2.7:
		// add_action( 'admin_menu', create_function( '$a', "remove_action( 'load-plugins.php', 'wp_update_plugins' );") );
		// add_action( 'admin_init', create_function( '$a', "remove_action( 'admin_init', 'wp_update_plugins' );"), 2 );
		// add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_update_plugins' );"), 2 );
		add_filter( 'pre_option_update_plugins', '__return_null' );

		# 2.8 to 3.0:
		remove_action( 'load-plugins.php', 'wp_update_plugins' );
		remove_action( 'load-update.php', 'wp_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'wp_update_plugins', 'wp_update_plugins' );
		add_filter( 'pre_transient_update_plugins', array( __CLASS__,'last_checked' ) );

		# >3.0:
		remove_action( 'load-update-core.php', 'wp_update_plugins' );
		add_filter( 'pre_site_transient_update_plugins', array( __CLASS__,'last_checked' ) );

		wp_clear_scheduled_hook( 'wp_update_plugins' );

		// Disable Plugin Update E-mails (only works for some plugins)
		// This doesn't make sense. Purpose?
		// apply_filters( 'auto_plugin_update_send_email', FALSE, $type, $plugin_update, $result );

		// This doesn't make sense. Purpose?
		// apply_filters( 'automatic_plugin_updates_send_debug_email', TRUE, $type, $plugin_update, $result );
	}

	// Disable Theme Updates
	static function disable_theme_updates() {

		# 2.8 to 3.0:
		remove_action( 'load-themes.php', 'wp_update_themes' );
		remove_action( 'load-update.php', 'wp_update_themes' );
		remove_action( 'admin_init', '_maybe_update_themes' );
		remove_action( 'wp_update_themes', 'wp_update_themes' );
		add_filter( 'pre_transient_update_themes', array( __CLASS__,'last_checked' ) );

		# >3.0:
		remove_action( 'load-update-core.php', 'wp_update_themes' );
		add_filter( 'pre_site_transient_update_themes', array( __CLASS__,'last_checked' ) );

		wp_clear_scheduled_hook( 'wp_update_themes' );

		// Disable Theme Update E-mails (only works for some plugins)
		// This doesn't make sense. Purpose?
		// apply_filters( 'auto_theme_update_send_email', FALSE, $type, $theme_update, $result );

		// This doesn't make sense. Purpose?
		// apply_filters( 'automatic_theme_updates_send_debug_email', TRUE, $type, $theme_update, $result );
	}

	static function plugin_block_action_link( $actions, $plugin_file, $plugin_data, $context ) {

		$blocked = get_option( 'disable_updates_blocked' );

		if ( $blocked !== FALSE && array_key_exists( $plugin_file, $blocked ) ) {

			$actions[] = '<a class="delete" href="plugins.php?_wpnonce=' . wp_create_nonce( 'disable_updates' ) . '&disable_updates&unblock=' . $plugin_file . '"><small>Updates Blocked</a>';

		} else {

			$actions[] = '<a class="delete" href="plugins.php?_wpnonce=' . wp_create_nonce( 'disable_updates' ) . '&disable_updates&block=' . $plugin_file . '"><small>Updates Allowed</small></a>';
		}

		return $actions;
	}
	
	static function http_request_args_plugins_filter( $r, $url ) {

		if ( 0 !== strpos( $url, 'https://api.wordpress.org/plugins/update-check/1.1/' ) ) {

			return $r;
		}

		$blocked = get_option( 'disable_updates_blocked' );
		$blocked = (array) array_keys( $blocked );

		if ( 0 === (int) count( $blocked ) ) {

			return $r;
		}

		if ( ! isset( $r['body']['plugins'] ) ) {

			return $r;
		}

		$plugins = json_decode( $r['body']['plugins'], TRUE );

		foreach ( $blocked as $p ) {

			if ( isset( $plugins['plugins'][ $p ] ) ) unset( $plugins['plugins'][ $p ] );
			if ( FALSE !== $key = array_search( $p, $plugins['active'] ) ) unset( $plugins['active'][ $key ] );
		}

		$r['body']['plugins'] = json_encode( $plugins );

		return $r;
	}

	static function http_request_args_themes_filter( $r, $url ) {

		if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) ) {

			return $r;
		}

		$options = get_option( '_disable_updates' );

		if ( FALSE === $options ) {

			return $r;
		}

		if ( isset( $options['themes'] ) ) {

			$blocked = $options['themes'];

		} else {

			return $r;
		}

		if ( 0 === (int) count( $blocked ) ) {

			return $r;
		}

		if ( ! isset( $r['body']['themes'] ) ) {

			return $r;
		}

		$themes = json_decode( $r['body']['themes'], TRUE );

		foreach ( $blocked as $t ) {

			if ( isset( $themes['themes'][ $t ] ) ) unset( $themes['themes'][ $t ] );
		}

		$r['body']['themes'] = json_encode( $themes );

		return $r;
	}
	
	// Help Tab
	static function help_tab() {
		global $test_help_page;

		$screen  = get_current_screen();
		$content1 = <<<CONTENT1
<p>	
This is the Disable Updates Manager settings help tab. In here you will find helpful information on what Disable Updates Manager does and how to use it.
</p>

<div class="error" id="error">			
	<p><strong>Please Note! - </strong>If either your WordPress core, theme, or plugins get too out
	   of date, you may run into compatibility problems. Check the capability tab for more information.</p>
</div>

CONTENT1;

		$content2 = <<<CONTENT2
<div><br></div>
<div id="acolumn1">
<iframe width="310" height="174" src="//www.youtube.com/embed/VYEQg-hZd58?rel=0" frameborder="0" allowfullscreen></iframe>
<iframe width="310" height="174" src="//www.youtube.com/embed/Kd4s3EOcUtw?rel=0" frameborder="0" allowfullscreen></iframe>
</div>

<div id="acolumn2">
<iframe width="310" height="174" src="//www.youtube.com/embed/1R-be48AvrE?rel=0" frameborder="0" allowfullscreen></iframe>
<iframe width="310" height="174" src="//www.youtube.com/embed/mYznDVsbVBk?rel=0" frameborder="0" allowfullscreen></iframe>
</div>

CONTENT2;

		$content3 = <<<CONTENT3
	<p>
        <a href="http://mpswp.wordpress.com" class="button">Our Website</a>
	    <a href="http://wordpress.org/support/plugin/stops-core-theme-and-plugin-updates" class="button">Support on WordPress</a>
	    <a href="https://github.com/Websiteguy/disable-updates-manager" class="button">GitHub Repository</a>
    </p>

CONTENT3;

		$content4 = <<<CONTENT4
<p>
Use the following features to customize the settings page as to suit your work-flow. 
<br>
<br>
<strong>Screen Options</strong> - Use the Screen Options tab to choose which metaboxes you would like to display.
<br>
<br>
<strong>Drag and Drop</strong> - To rearrange the boxes, drag and drop by clicking on the title bar of the selected metabox and releasing when you see a gray dotted-line appear in the location you want to place the box.
<br>
<br>
<strong>Box Controls</strong> - Click the title bar of the box to expand or collapse the contents.
<br>
<br>
<strong>Chosen</strong> - Check the Disable Themes Individually setting and/or the Disable Plugins Individually settings to enable the chosen multiply select box.
<br>
<br>
<strong>Tooltips</strong> - If you are having trouble figuring out how a setting works, just wave over the "?". It will display a little bubble with helpful information.
</p>

CONTENT4;

		$content5 = <<<CONTENT5
		
<p style="align: center;">
<h3>Contributors:</h3>
<ul>
<li><a href="http://profiles.wordpress.org/kidsguide/">Matthew</a></li>
<li><a href="http://profiles.wordpress.org/mps-plugins/">MPS Plugins</a></li>
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
<li>Tested with WordPress 4.0.1 and 4.1beta1.</li>
<li>Tested with popular plugins to ensure that there are no conflicts.</li>
<li>Tested with popular themes to ensure that there are no conflicts.</li>
</ul>
</p>

CONTENT6;

		// Add my_help_tab if current screen is My Admin Page
		$screen->add_help_tab(array(
				'id'      => 'help_tab_content_1',
				'title'   => __('Overview'),
				'content' => $content1,
			));
			
	    $screen->add_help_tab(array(
                'id' => 'help_tab_content_4',
                'title' => __('Layout'),
                'content' => $content4,
            ));
			
	    $screen->add_help_tab(array(
                'id' => 'help_tab_content_2',
                'title' => __('Tutorials'),
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
	
	}
	
	static function metabox_global( $status ) {

		?>
		<div>
			<p>
		    <div class="showonhover">
				<label for="all_notify">
					<input
						type="checkbox" <?php checked( 1, ( isset( $status['all'] ) ? (int) $status['all'] : 0 ), TRUE ); ?>
						value="1" id="all_notify"
						name="_disable_updates[all]"> <?php _e( 'Disable All Updates', 'disable-updates-manager' ) ?>
				</label>
				<span>
					<a href="#" class="viewdescription">?</a>
					<span class="hovertext">Disables core, theme, and plugin updates.</span>
				</span>
		    </div>
			</p>
		</div>

		<div style="padding-left: 12px;">
			<p>
			<div>
				<label for="plugins_notify">
					<input type="checkbox" <?php checked( 1, ( isset( $status['plugin'] ) && ! isset( $status['all'] ) ? (int) $status['plugin'] : 0 ), TRUE ); ?>
						   value="1" id="plugins_notify"
						   name="_disable_updates[plugin]"
						   <?php disabled( 1, ( isset( $status['all'] ) ? (int) $status['all'] : 0 ) ) ?>> <?php _e( 'Disable All Plugin Updates', 'disable-updates-manager' ) ?>
				</label>
			</div>
			<br>
			<div>
				<label for="themes_notify">
					<input type="checkbox" <?php checked( 1, ( isset( $status['theme'] ) && ! isset( $status['all'] ) ? (int) $status['theme'] : 0 ), TRUE ); ?>
						   value="1" id="themes_notify"
						   name="_disable_updates[theme]"
						   <?php disabled( 1, ( isset( $status['all'] ) ? (int) $status['all'] : 0 ) ) ?>> <?php _e( 'Disable All Theme Updates', 'disable-updates-manager' ) ?>
				</label>
			</div>
            <br>
			<div>
				<label for="core_notify">
					<input type="checkbox" <?php checked( 1, ( isset( $status['core'] ) && ! isset( $status['all'] ) ? (int) $status['core'] : 0 ), TRUE ); ?>
						   value="1" id="core_notify"
						   name="_disable_updates[core]"
						   <?php disabled( 1, ( isset( $status['all'] ) ? (int) $status['all'] : 0 ) ) ?>> <?php _e( 'Disable All WordPress Core Update', 'disable-updates-manager' ) ?>
				</label>
			</div>
		<div style="padding-left:20px;">
		<br>
	<small>Disable major and minor core feature coming soon...<small>
			</p>
		</div>	
		</div>

		<?php
	}

	static function metabox_other( $status ) {

		?>
		<div>
		    <p>
			<div class="showonhover">
				<label for="page_notify">
					<input
						type="checkbox" <?php checked( 1, ( isset( $status['page'] ) ? (int) $status['page'] : 0 ), TRUE ); ?>
						value="1" id="page_notify"
						name="_disable_updates[page]"> <?php _e( 'Remove the Updates Page', 'disable-updates-manager' ) ?>
				</label>
				<span>
					<a href="#" class="viewdescription">?</a></a>
					<span class="hovertext">The page under the Dashboard tab.</span>
				</span>
			</div>
			<br>
            <div class="showonhover">
				<label for="bnag_notify">
					<input type="checkbox" <?php checked( 1, ( isset( $status['bnag'] ) ? (int) $status['bnag'] : 0 ), TRUE ); ?>
						   value="1" id="bnag_notify"
						   name="_disable_updates[bnag]"> <?php _e( 'Disable Out of Date Browser Nag', 'disable-updates-manager' ) ?>
				</label>
				<span>
					<a href="#" class="viewdescription">?</a>
					<span class="hovertext">Removes the browsers is out of date notification in you WordPress dashboard.</span>
				</span>
			</div>
			<br>
			<div class="showonhover">
				<label for="wpv_notify">
					<input
						type="checkbox" <?php checked( 1, ( isset( $status['wpv'] ) ? (int) $status['wpv'] : 0 ), TRUE ); ?>
						value="1" id="wpv_notify"
						name="_disable_updates[wpv]"> <?php _e( 'Disable WordPress Core Version from Footer', 'disable-updates-manager' ) ?>
				</label>
				<span>
					<a href="#" class="viewdescription">?</a>
					<span class="hovertext">Removes it for all users.</span>
				</span>
			</div>
            <br>
            <div class="showonhover">
				<label for="auto-translation-updates_notify">
					<input type="checkbox" <?php checked( 1, ( isset( $status['auto-translation-updates'] ) ? (int) $status['auto-translation-updates'] : 0 ), TRUE ); ?>
						   value="1" id="auto-translation-updates_notify"
						   name="_disable_updates[auto-translation-updates]"> <?php _e( 'Disable Automatic Translation Updates', 'disable-updates-manager' ) ?>
				</label>
				<span>
					<a href="#" class="viewdescription">?</a>
					<span class="hovertext">Disables the automatic translation updates for you.</span>
				</span>
			</div>
			<br>
			<div class="showonhover">
				<label for="auto-core-emails_notify">
					<input
						type="checkbox" <?php checked( 1, ( isset( $status['auto-core-emails'] ) ? (int) $status['auto-core-emails'] : 0 ), TRUE ); ?>
						value="1" id="auto-core-emails_notify"
						name="_disable_updates[auto-core-emails]"> <?php _e( 'Disable Core Update E-mails', 'disable-updates-manager' ) ?>
				</label>
				<span>
					<a href="#" class="viewdescription">?</a>
					<span class="hovertext">Disables the core update e-mails so that they will not be sent to you.</span>
				</span>
			</div>
            <br>
			<div>
				<label for="abup_notify">
					<input type="checkbox" <?php checked( 1, ( isset( $status['abup'] ) ? (int) $status['abup'] : 0 ), TRUE ); ?>
						   value="1" id="abup_notify"
						   name="_disable_updates[abup]"> <?php _e( 'Disable Automatic Background Updates', 'disable-updates-manager' ) ?>
				</label>
			</p>
			</div>
		</div>

		<?php
	}

	static function metabox_themes( $status ) {

		?>
		<div>
			<p>
		<div class="showonhover">
				<label for="dum-disable-themes">
					<input type="checkbox" <?php checked( 1, ( isset( $status['it'] ) && ! isset( $status['all'] ) ? (int) $status['it'] : 0 ), TRUE ); ?>
						   id="dum-disable-themes"
						   value="1"
						   name="_disable_updates[it]"
						   <?php disabled( 1, ( isset( $status['all'] ) ? (int) $status['all'] : 0 ) ) ?>> <?php _e( 'Disable Themes Individually', 'disable-updates-manager' ) ?>
				</label>
				<span>
					<a href="#" class="viewdescription">?</a>
					<span class="hovertext">Enabling this option will show the list of themes to disable updates.</span>
				</span>
			</p>
		</div>
        </div>
		<?php

		$themes = wp_get_themes( array( 'allowed' => TRUE ) );

		if ( ! empty( $themes ) ) {

			echo '<select class="dum-enhanced-select" id="dum-disable-themes-select" name="_disable_updates[themes][]" data-placeholder="' . __( 'Select theme(s) to disable...', 'disable-updates-manager' ) . '" multiple' . ( isset( $status['it'] ) && ! isset( $status['all'] ) ? '' : ' disabled' ) . '>';

				echo '<option value=""></option>';

				foreach ( $themes as $slug => $theme ) {

					printf( '<option value="%1$s"%2$s>%3$s</option>',
						esc_attr( $slug ),
						! isset( $status['all'] ) && isset( $status['it'] ) && isset( $status['themes'] ) && in_array( $slug, $status['themes'] ) ?  ' SELECTED' : '',
						esc_html( $theme->name )
					);
				}

			echo '</select>';
		}

	}

	static function metabox_plugins( $status ) {

		?>
			<p>
		<div class="showonhover">
				<label for="dum-disable-plugins">
					<input type="checkbox" <?php checked( 1, ( isset( $status['ip'] ) && ! isset( $status['all'] ) ? (int) $status['ip'] : 0 ), TRUE ); ?>
						   id="dum-disable-plugins"
						   value="1"
						   name="_disable_updates[ip]"
						   <?php disabled( 1, ( isset( $status['all'] ) ? (int) $status['all'] : 0 ) ) ?>> <?php _e( 'Disable Plugins Individually', 'disable-updates-manager' ) ?>
				</label>
				<span>
					<a href="#" class="viewdescription">?</a>
					<span class="hovertext">Enabling this option will show the list of plugins to disable updates.</span>
				</span>
			</p>
		</div>

		<?php

		$plugins = get_plugins();
		$blocked = get_option( 'disable_updates_blocked' );

		if ( ! empty( $plugins ) ) {

			echo '<select class="dum-enhanced-select" id="dum-disable-plugins-select" name="_disable_updates[plugins][]" data-placeholder="' . __( 'Select plugin(s) to disable...', 'disable-updates-manager' ) . '" multiple' . ( isset( $status['ip'] ) && ! isset( $status['all'] ) ? '' : ' disabled' ) . '>';

				echo '<option value=""></option>';

				foreach ( $plugins as $slug => $plugin ) {

					printf( '<option value="%1$s"%2$s>%3$s</option>',
						esc_attr( $slug ),
						! isset( $status['all'] ) && isset( $status['ip'] ) && array_key_exists( $slug, (array) $blocked ) ?  ' SELECTED' : '',
						esc_attr( $plugin['Name'] )
					);
				}

			echo '</select>';
		}

	}
	
	// Settings page (under dashboard).
	static function display_page() {
	?>
	
<h2><?php _e( 'Disable Updates Manager', 'disable-updates-manager' ); ?></h2>
        
		<?php 
		
		if ( !current_user_can('manage_options') ) {
		wp_die('You do not have sufficient permissions to access this page.');
		}
		
		if ( current_user_can('manage_options') ) {
		
		$status = get_option( '_disable_updates' );
 
		?>

		<div class="dashboard-widgets-wrap">
           
			<div id="dashboard-widgets" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

				<form name="dum-options" method="post" action="options.php">
					<input type="hidden" name="action" value="dum-update-options">
					<?php wp_nonce_field( 'dum-update-options-nonce' );

					/* Used to save closed metaboxes and their order */
					wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', FALSE );
					wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', FALSE ); ?>

					<div class="postbox-container"> 
						<?php do_meta_boxes( self::$page_hook, 'left', $status ); ?> 
					</div> 
 
					<div class="postbox-container"> 
						<?php do_meta_boxes( self::$page_hook, 'right', $status );  ?> 
						<?php //do_meta_boxes( self::$page_hook, 'advanced', $status ); ?> 
					</div> 

					<?php settings_fields( '_disable_updates' ); ?>

					<p class="submit clear">
						<input type="submit" class="button-primary" value="<?php _e( 'Update Settings' ) ?>"/> 
						<a href="https://wordpress.org/support/view/plugin-reviews/stops-core-theme-and-plugin-updates?#postform" style="background-color:#ffff7f" class="button">Rate Plugin</a>	
						<a href="http://sparrowkids2.polldaddy.com/s/disable-updates-manager-feedbeck" class="button">Feedback Survey</a>				
					</p>

				</form>

			</div><!-- #dashboard-widgets -->

		</div><!-- .dashboard-widgets-wrap -->
		
	<?php
	}
}

}

global $Disable_Updates;
$Disable_Updates = new Disable_Updates();

if ( ! function_exists( 'printr' ) ) {
	function printr( $txt ) {
		echo '<pre>';
		print_r( $txt );
		echo '</pre>';
	}
}
