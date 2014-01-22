<?php
/**
 * @package Disable Updates Manager
 * @author Websiteguy
 * @version 3.1.0
*/
/*
Plugin Name: Disable Updates Manager
Plugin URI: http://wordpress.org/plugins/stops-core-theme-and-plugin-updates/
Version: 3.1.0
Description: Pick which type of updates you would like to disable. Just use are settings forum.
Author: Websiteguy
Author URI: http://profiles.wordpress.org/kidsguide/
Tested up to WordPress 3.8.
*/
/*
License:

@Copyright 2013 - 2014 Websiteguy (email : mpsparrow@cogeco.ca)

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
*/

define("DISABLEUPDATESMANAGERVERSION", "3.1.0");

    class Disable_Updates {
	    // Set status in array
	    private $status = array(); 
		
		// Set checkboxes in array
	    private $checkboxes = array(); 
	
	function Disable_Updates() {
		
	// Add menu page
	        add_action('admin_menu', array(&$this, 'add_submenu'));
		
	// Settings API
		    add_action('admin_init', array(&$this, 'register_setting'));
		
		
	// load the values recorded
		    $this->load_disable_updates();
		}

	// Register Settings
	function register_setting()	{
	    register_setting('_disable_updates', '_disable_updates', array(&$this, 'validate_settings'));		
	    }

	function validate_settings( $input ) {
		$options = get_option( '_disable_updates' );
		
		foreach ( $this->checkboxes as $id ) {
			if ( isset( $options[$id] ) && !isset( $input[$id] ) )
				unset( $options[$id] );
		}
		
		return $input;
	    }

	function add_submenu() {
	// Add submenu in menu "Dashboard"
		add_submenu_page( 'index.php', 'Disable Updates', __('Disable Updates','disable-updates-manager'), 'administrator', __FILE__, array(&$this, 'display_page') );
		}

		
	// Functions for Plugin (Change in Settings)	
	function load_disable_updates() {
		$this->status = get_option('_disable_updates');
		
		if( !$this->status ) return;

		foreach( $this->status as $id => $value ) {

		switch( $id ) {

	// Disable Plugin Updates	
			case 'plugin' :
					
    // Disable Plugin Updates Code
		remove_action( 'load-update-core.php', 'wp_update_plugins' );
		add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );

    // Disable Plugin Update E-mails (only works for some plugins)
		apply_filters( 'auto_plugin_update_send_email', false, $type, $plugin_update, $result );
			
			break;
	
	// Disable Theme Updates
			case 'theme' :

    // Disable Theme Updates Code
		remove_action( 'load-update-core.php', 'wp_update_themes' );
		add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );

    // Disable Theme Update E-mails (only works for some plugins)
		apply_filters( 'auto_theme_update_send_email', false, $type, $theme_update, $result );

			break;
				
	// Disable WordPress Core Updates			
			case 'core' :
	
    // Disable WordPress Core Updates Code
		remove_action( 'load-update-core.php', 'wp_update_core' );
		add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );

    // Disable WordPress Core Update E-mails (only works for some plugins)
		apply_filters( 'auto_core_update_send_email', false, $type, $core_update, $result );
					
			break;

	// Remove the Dashboard Updates Menu		
			case 'page' :
			
	// Remove the Dashboard Updates Menu Code		
		add_action( 'admin_init', 'wpse_38111' );
		function wpse_38111() {
		    remove_submenu_page( 'index.php', 'update-core.php' );
		}
					
			break;

    // Disable All Updates 
			case 'all' :

    // Disable All Updates

    // Disable Plugin Updates Only

		remove_action( 'load-update-core.php', 'wp_update_plugins' );
		add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );

    // Disable Theme Updates Only

		remove_action( 'load-update-core.php', 'wp_update_themes' );
		add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );

    // Disable Core Updates Only

		remove_action( 'load-update-core.php', 'wp_update_core' );
		add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );

    // Hide Update Notices in Admin Dashboard

		add_action('admin_menu','hide_admin_notices');
		function hide_admin_notices() {
		remove_action( 'admin_notices', 'update_nag', 3 );
		}

    // Remove Files From WordPress

		function admin_init() {
		if ( !function_exists("remove_action") ) return;

    // Disable Plugin Updates Only

		remove_action( 'load-plugins.php', 'wp_update_plugins' );
		remove_action( 'load-update.php', 'wp_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'wp_update_plugins', 'wp_update_plugins' );
		wp_clear_scheduled_hook( 'wp_update_plugins' );
		
		remove_action( 'load-update-core.php', 'wp_update_plugins' );
		wp_clear_scheduled_hook( 'wp_update_plugins' );	

    // Disable Theme Updates Only

		remove_action( 'load-themes.php', 'wp_update_themes' );
		remove_action( 'load-update.php', 'wp_update_themes' );
		remove_action( 'admin_init', '_maybe_update_themes' );
		remove_action( 'wp_update_themes', 'wp_update_themes' );
		wp_clear_scheduled_hook( 'wp_update_themes' );
		
		remove_action( 'load-update-core.php', 'wp_update_themes' );
		wp_clear_scheduled_hook( 'wp_update_themes' );

    // Disable Core Updates Only

		remove_action( 'wp_version_check', 'wp_version_check' );
		remove_action( 'admin_init', '_maybe_update_core' );
		wp_clear_scheduled_hook( 'wp_version_check' );
		
		wp_clear_scheduled_hook( 'wp_version_check' );
		}

    // Remove Updates Again (different method) 

		add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );

		add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_version_check' );" ), 2 );
		add_filter( 'pre_option_update_core', create_function( '$a', "return null;" ) );

		remove_action( 'wp_version_check', 'wp_version_check' );
		remove_action( 'admin_init', '_maybe_update_core' );
		add_filter( 'pre_transient_update_core', create_function( '$a', "return null;" ) );

		add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );

		remove_action( 'load-themes.php', 'wp_update_themes' );
		remove_action( 'load-update.php', 'wp_update_themes' );
		remove_action( 'admin_init', '_maybe_update_themes' );
		remove_action( 'wp_update_themes', 'wp_update_themes' );
		add_filter( 'pre_transient_update_themes', create_function( '$a', "return null;" ) );

		remove_action( 'load-update-core.php', 'wp_update_themes' );
		add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );

		add_action( 'admin_menu', create_function( '$a', "remove_action( 'load-plugins.php', 'wp_update_plugins' );") );
	
		add_action( 'admin_init', create_function( '$a', "remove_action( 'admin_init', 'wp_update_plugins' );"), 2 );
		add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_update_plugins' );"), 2 );
		add_filter( 'pre_option_update_plugins', create_function( '$a', "return null;" ) );

		remove_action( 'load-plugins.php', 'wp_update_plugins' );
		remove_action( 'load-update.php', 'wp_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'wp_update_plugins', 'wp_update_plugins' );
		add_filter( 'pre_transient_update_plugins', create_function( '$a', "return null;" ) );

		remove_action( 'load-update-core.php', 'wp_update_plugins' );
		add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
		
		
		// Disable Debug E-mails
		add_filter( 'automatic_updates_send_debug_email ', '__return_false', 1 );
		
		// Disable WordPress Automatic Updates
		define( 'Automatic_Updater_Disabled', true );
        define('WP_AUTO_UPDATE_CORE', false);
		
		// Disable Updates E-mails
		
		// Core E-mails Only
		apply_filters( 'auto_core_update_send_email', false, $type, $core_update, $result );

        // Plugin E-mails Only
		apply_filters( 'auto_plugin_update_send_email', false, $type, $plugin_update, $result );

        // Theme E-mails Only
		apply_filters( 'auto_theme_update_send_email', false, $type, $theme_update, $result );
		

			break;

    // Remove WordPress Version Number
			case 'wpv' :
				
	    add_filter('admin_footer_text', 'replace_footer_admin');
	    function replace_footer_version() 
	    {
		    return ' ';
	    }
	    
	    add_filter( 'update_footer', 'replace_footer_version', '1234');

            break;
            
case 'abup' :
wp_clear_scheduled_hook( 'wp_maybe_auto_update' ); 	
break;
}
	}
}

    // Settings Page (Under Dashboard)
	    function display_page() { 
		
	// Don't Allow Users to View Settings
		if (!current_user_can('update_core'))
			wp_die( __('You do not have permissions to access this page.') );
		
		?>
	
		<div class="wrap">  
			<h2><?php _e('Disable Updates Manager Settings','disable-updates-manager'); ?></h2>
			
			<form method="post" action="options.php">
				
			    <?php settings_fields('_disable_updates'); ?>
			    			    
				<table class="form-table">
					<tr>
					<td>
						<fieldset>
		<div class="postbox">
			<H3>&nbsp;Disable Updates</H3> 
		<div class="inside">
							<label for="all_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['all'], true); ?> value="1" id="all_notify" name="_disable_updates[all]"> <?php _e('Disable All Updates <small>(Not including the settings under "Other Settings")</small>', 'disable-updates-manager') ?>
							</label>
							    <br>
							<span style="padding-left: 20px; display:block">
							<label for="plugins_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['plugin'], true); ?> value="1" id="plugins_notify" name="_disable_updates[plugin]"> <?php _e('Disable Plugin Updates', 'disable-updates-manager') ?>
							</label>
								<br>
							<label for="themes_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['theme'], true); ?> value="1" id="themes_notify" name="_disable_updates[theme]"> <?php _e('Disable Theme Updates', 'disable-updates-manager') ?>
							</label>
								<br>
							<label for="core_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['core'], true); ?> value="1" id="core_notify" name="_disable_updates[core]"> <?php _e('Disable WordPress Core Update', 'disable-updates-manager') ?>
							</label>
							</span>
		</div>
		</div>
						</fieldset>
					</td>
				    </tr>

				    <tr>
					<td>
						<fieldset>
		<div class="postbox">
			<H3>&nbsp;Other Settings</H3>
		<div class="inside">
<span style="padding-left: 0px; display:block">
							<label for="abup_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['abup'], true); ?> value="1" id="abup_notify" name="_disable_updates[abup]"> <?php _e('Disable Automatic Background Updates', 'disable-updates-manager') ?>
							</label>
<br>
							<label for="wpv_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['wpv'], true); ?> value="1" id="wpv_notify" name="_disable_updates[wpv]"> <?php _e('Remove WordPress Core Version <small>(For All Users)</small>', 'disable-updates-manager') ?>
							</label>
<br>
							<label for="page_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['page'], true); ?> value="1" id="page_notify" name="_disable_updates[page]"> <?php _e('Remove Updates Page <small>(Under Dashboard)</small>', 'disable-updates-manager') ?>
							</label>
</span>
		</div>
		</div>
								<p class="submit">
									<input type="submit" class="button-primary" value="<?php _e('Update Settings') ?>" />
								</p>
						</fieldset>
					</td>
					</tr>				

				    <tr>
				    <br>
<span style="border-style:solid; border-width:2px; border-color:#dd0606; display:block">
		                    <p align="center">
<strong>Please Note! - </strong>If either your WordPress core, theme, or plugins get to out of date, you may run into compatibility problems.
				    </p>		
</span>
		</div>
		</div>
					</tr>

                </table>

			</form>

		</div>
	
<?php
		}	
	}

    // Start this plugin once all other plugins are fully loaded
		global $Disable_Updates; $Disable_Updates = new Disable_Updates();
		
    // Plugin Page Link Function
		add_filter( 'plugin_row_meta', 'thsp_plugin_meta_links', 10, 2 );

		function thsp_plugin_meta_links( $links, $file ) {	
		    $plugin = plugin_basename(__FILE__);	

    // Create links
		if ( $file == $plugin ) {		
		    return array_merge(			
		    $links,	
		        array( '<a href="http://www.wordpress.org/support/plugin/stops-core-theme-and-plugin-updates">Support</a>' ),
		        array( '<a href="http://www.wordpress.org/plugins/stops-core-theme-and-plugin-updates/faq/">FAQ</a>' ),
		        array( '<a href="http://www.youtube.com/watch?v=jAqd0SjLQ_M">Tutorial</a>' )
		);	
		}	
		return $links;
	}

    // Add Settings Link
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'thsp_plugin_action_links' );

		function thsp_plugin_action_links( $links ) {

		return array_merge(
			array('settings' => '<a href="' . admin_url( 'index.php?page=stops-core-theme-and-plugin-updates/Function.php' ) . '">' . __( 'Settings', 'ts-fab' ) . '</a>'),
				$links);
		}
