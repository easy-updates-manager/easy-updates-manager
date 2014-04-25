<?php
/**
 * @package Disable Updates Manager
 * @author Websiteguy
 * @version 4.0
*/
/*
Plugin Name: Disable Updates Manager
Plugin URI: http://wordpress.org/plugins/stops-core-theme-and-plugin-updates/
Version: 4.0
Description: Pick which type of updates you would like to disable. Just use the settings.
Author: Websiteguy
Author URI: http://profiles.wordpress.org/kidsguide/
License: GPL2
Text Domain: stops-core-theme-and-plugin-updates
Domain Path: /lang
Tested up to WordPress 3.9
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

or go to the license.txt in the trunk.
*/

// Define version.

	define("DISABLEUPDATESMANAGERVERSION", "4.0");

    class Disable_Updates {
	    // Set status in array
	    private $status = array(); 
		
		// Set checkboxes in array
	    private $checkboxes = array(); 
	  
function Disable_Updates() { 
		
// Add menu page.
	        add_action('admin_menu', array(&$this, 'add_submenu'));
		
// Settings API.
		    add_action('admin_init', array(&$this, 'register_setting'));
		
		
// load the values recorded.
			$this->load_disable_updates();
}
	
// Register settings.
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
// Add submenu to "Dashboard" menu.
add_submenu_page( 'options-general.php', 'Disable Updates Manager', __('Disable Updates Manager','disable-updates-manager'), 'administrator', __FILE__, array(&$this, 'display_page') );
		}

	  // Functions for plugin (Change in settings)	
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
	
		apply_filters('automatic_plugin_updates_send_debug_email', true, $type, $plugin_update, $result );
			
			break;
	
// Disable Theme Updates
			case 'theme' :

    // Disable Theme Updates Code
		remove_action( 'load-update-core.php', 'wp_update_themes' );
		add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );

    // Disable Theme Update E-mails (only works for some plugins)
	    apply_filters( 'auto_theme_update_send_email', false, $type, $theme_update, $result );
	
		apply_filters('automatic_theme_updates_send_debug_email', true, $type, $theme_update, $result );

			break;
				
// Disable WordPress Core Updates			
			case 'core' :
	
    // Disable WordPress Core Updates Code
		remove_action( 'load-update-core.php', 'wp_update_core' );
		add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );

    // Disable WordPress Core Update E-mails (only works for some plugins)
		apply_filters( 'auto_core_update_send_email', false, $type, $core_update, $result );
		
		apply_filters('automatic_core_updates_send_debug_email', true, $type, $core_update, $result );
					
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
		add_filter( 'automatic_updates_send_debug_email ', '__return_true', 1 );
		
		// Disable WordPress Automatic Updates
		define( 'Automatic_Updater_Disabled', true );
        define('WP_AUTO_UPDATE_CORE', false);
		
		// Disable Updates E-mails
		
		// Core E-mails Only
		apply_filters( 'auto_core_update_send_email', false, $type, $core_update, $result );
		
		apply_filters('automatic_core_updates_send_debug_email', true, $type, $core_update, $result );

        // Plugin E-mails Only
		apply_filters( 'auto_plugin_update_send_email', false, $type, $plugin_update, $result );

		apply_filters('automatic_plugin_updates_send_debug_email', true, $type, $plugin_update, $result );
		
        // Theme E-mails Only
		apply_filters( 'auto_theme_update_send_email', false, $type, $theme_update, $result );
		
		apply_filters('automatic_theme_updates_send_debug_email', true, $type, $theme_update, $result );
		
			break;

// Remove WordPress Version Number
			case 'wpv' :

add_filter( 'update_footer', 'my_footer_version', 11 );
  
function my_footer_version() {
    return '';
}

            break;

case 'ip' :
if(defined('disable_updates_loaded')) {
        return;
}
define('disable_updates_loaded', 1);

add_action('init','disable_updates_get');
add_action('init','disable_updates_addFilters');

add_filter("plugin_row_meta", 'disable_updates_pluginLinks', 10, 2);
add_filter('site_transient_update_plugins', 'disable_updates_blockUpdateNotifications');

function disable_updates_addFilters() {
        if(!current_user_can('update_plugins')) {
                return;
        }

        $plugins = get_site_transient('update_plugins');
        $to_block = get_option('disable_updates_blocked');
	
	if(isset($plugins->response)) {
	        // loop through all of the plugins with updates available and attach the appropriate filter
		foreach($plugins->response as $filename => $plugin) {
                	// check that the version is the version we want to block updates to
                	$s = 'after_plugin_row_' . $filename;
                	//in_plugin_update_message-
                	add_action($s, 'disable_updates_blockLink', -1, 1);
		}
        }
	if(isset($plugins->disable_updates)) {
        	foreach($plugins->disable_updates as $filename => $plugin) {
                	// check that the version is the version we want to block updates to
                	$s = 'after_plugin_row_' . $filename;
                	add_action($s, 'disable_updates_unblockLink', 2, 1);
		}
        }
}

function disable_updates_get() {

        if(!current_user_can('update_plugins')) {
                return;
        }

        // see if there are actions to process
        if(!isset($_GET['disable_updates']) || !isset($_GET['_wpnonce'])) {
                return;
        }

        if(!wp_verify_nonce($_GET['_wpnonce'], 'disable_updates')) {
                return;
        }

        $blocked = get_option('disable_updates_blocked');
        $plugins = get_site_transient('update_plugins');

        // block action
        if(isset($_GET['block']) && isset($plugins->response) && isset($plugins->response[$_GET['block']])) {
                $p = $plugins->response[$_GET['block']];
                $blocked[$_GET['block']] = array('slug' => $p->slug, 'new_version' => $p->new_version);
        }

        if(isset($_GET['unblock'])) {
                unset($blocked[$_GET['unblock']]);

        }

        update_option('disable_updates_blocked', $blocked);

}

function disable_updates_blockUpdateNotifications($plugins) {

	if(!isset($plugins->response) || count($plugins->response) == 0) {
		return $plugins;
	}

        $to_block = (array)get_option('disable_updates_blocked');

        foreach($to_block as $filename => $plugin) {

                if(isset($plugins->response[$filename])
                        && $plugins->response[$filename]->new_version == $plugin['new_version']) {

                        $plugins->disable_updates[$filename] = $plugins->response[$filename];
                        unset($plugins->response[$filename]);
                }
        }
        return $plugins;
}

function disable_updates_unblockLink($filename) {
        disable_updates_linkStart();
        echo 'Updates for this plugin are blocked. <a href="plugins.php?_wpnonce=' . wp_create_nonce('disable_updates') . '&disable_updates&unblock=' . $filename . '">Unblock updates</a>.</div></td></tr>';
}

function disable_updates_blockLink($filename) {
        disable_updates_linkStart();
        echo ' <a href="plugins.php?_wpnonce=' . wp_create_nonce('disable_updates') . '&disable_updates&block=' . $filename . '">Block updates for this plugin</a>.</div></td></tr>';
}

function disable_updates_linkStart() {

        // wp_plugin_update_row
        // wp-admin/includes/update.php

        $wp_list_table = _get_list_table('WP_Plugins_List_Table');
        echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message">';
}

function disable_updates_pluginLinks( $links, $file ) {
        $plugin = plugin_basename(__FILE__);
	if($file == $plugin) {
		$links[] = '<a target="_BLANK" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LC5UR6667DLXU"></a>';
        }
        return $links;
}

if(!function_exists('printr')) {
        function printr($txt) {
                echo '<pre>'; print_r($txt); echo '</pre>';
        }
}
break;
        
// Disable automatic background updates.		
	case 'abup' :
	wp_clear_scheduled_hook( 'wp_maybe_auto_update' ); 	
	break;

	}
	}
	}
	  
	// Settings page (under dashboard).
	    function display_page() { 
	
	// Don't Allow Users to View Settings
		if (!current_user_can('update_core'))
			wp_die( __('You do not have permissions to access this page.') );
		
		?>

		<div class="wrap">  
<span style="display:block; padding-left: 5px; padding-bottom: 5px">
			<h2><?php _e('Disable Updates Manager Settings','disable-updates-manager'); ?></h2>
</span>
			
			<form method="post" action="options.php">
				
			    <?php settings_fields('_disable_updates'); ?>
			    			    
				<table class="form-table">
					<tr>

</table>
<table class="wp-list-table widefat fixed bookmarks" style="width: 590px; border-radius: 4px;">
<thead>
<tr>
			<th>Disable Updates</th> 
</tr>
</thead>
<tbody>
<tr>
<td>

	<div class="showonhover">
							<label for="all_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['all'], true); ?> value="1" id="all_notify" name="_disable_updates[all]"> <?php _e('Disable All Updates', 'disable-updates-manager') ?>
							</label>
 	<span>
 	<a href="#" class="viewdescription">?</a>
 	<span class="hovertext">Just disables core, theme, and plugin updates.</span>
 	</span>
 	</div>
	</span>
							<span style="padding-left: 12px; display:block">
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
</td>
</tr>
</tbody>
</table>
<br>

<table class="wp-list-table widefat fixed bookmarks" style="width: 590px; border-radius: 4px;">
<thead>
<tr>
			<th>Disable Plugins Individually</th> 
</tr>
</thead>
<tbody>
<tr>
<td>
	<div class="showonhover">
							<label for="ip_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['ip'], true); ?> value="1" id="ip_notify" name="_disable_updates[ip]"> <?php _e('Disable Plugins Individually', 'disable-updates-manager') ?>
							</label>
							
 	<span>
 	<a href="#" class="viewdescription">?</a>
 	<span class="hovertext">Go to the "Plugins" section in your dashboard to disable.</span>
 	</span>
 	</div>
	</span>
</td>
</tr>
</tbody>
</table>
<br>
<table class="wp-list-table widefat fixed bookmarks" style="width: 590px; border-radius: 4px;">
<thead>
<tr>
			<th>Other Settings</th>
</tr>
</thead>
<tbody>
<tr>
<td>
	<div class="showonhover">
							<label for="page_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['page'], true); ?> value="1" id="page_notify" name="_disable_updates[page]"> <?php _e('Remove Updates Page', 'disable-updates-manager') ?>
							</label>
 	<span>
 	<a href="#" class="viewdescription">?</a>
 	<span class="hovertext">The one in the dashboard tab.</span>
 	</span>
 	</div>
	</span>
	<div class="showonhover">
							<label for="wpv_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['wpv'], true); ?> value="1" id="wpv_notify" name="_disable_updates[wpv]"> <?php _e('Remove WordPress Core Version', 'disable-updates-manager') ?>
							</label>
 	<span>
 	<a href="#" class="viewdescription">?</a>
 	<span class="hovertext">Removes it for all users.</span>
 	</span>
 	</div>
</span>
							<label for="abup_notify">
									<input type="checkbox" <?php checked(1, (int)$this->status['abup'], true); ?> value="1" id="abup_notify" name="_disable_updates[abup]"> <?php _e('Disable Automatic Background Updates', 'disable-updates-manager') ?>
							</label>
<br>
		</td>
</tr>
</tbody>
</table>							
<p class="submit">
									<input type="submit" class="button-primary" value="<?php _e('Update Settings') ?>" />
	</p>

<table class="wp-list-table widefat fixed bookmarks" style="width: auto; padding: 5px; border-radius: 4px;">
<tbody>
<tr>
<td>
		                    <p align="center">
	<a href="http://wordpress.org/support/plugin/stops-core-theme-and-plugin-updates">Support</a> | <a href="http://www.youtube.com/watch?v=7sMEBGNxhwA">Tutorial</a> | <a href="http://wordpress.org/plugins/stops-core-theme-and-plugin-updates/faq/">FAQ</a> | <a href="https://github.com/Websiteguy/disable-updates-manager">GitHub</a>
				    </p>	
</td>
</tr>
</tbody>
</table>
<div class="error" style="width: 780px;">
<p>
<strong>Please Note! - </strong>If either your WordPress core, theme, or plugins get too out of date, you may run into compatibility problems.
</p>
</div>
			</form>
		</div>
	
	<?php
		}	
	}

// Start Disable Updates Manager once all other plugins are fully loaded.
		global $Disable_Updates; $Disable_Updates = new Disable_Updates();
		
// Plugin page link.
		add_filter( 'plugin_row_meta', 'thsp_plugin_meta_links', 10, 2 );

		function thsp_plugin_meta_links( $links, $file ) {	
		    $plugin = plugin_basename(__FILE__);	

    // Create links.
		if ( $file == $plugin ) {		
		    return array_merge(			
		    $links,	
		        array( '<a href="http://www.wordpress.org/support/plugin/stops-core-theme-and-plugin-updates">Support</a>' ),
		        array( '<a href="http://www.wordpress.org/plugins/stops-core-theme-and-plugin-updates/faq/">FAQ</a>' ),
		        array( '<a href="http://www.youtube.com/watch?v=7sMEBGNxhwA">Tutorial</a>' ),
				array( '<a href="https://github.com/Websiteguy/disable-updates-manager">GitHub</a>' )
		);	
		}	
		return $links;
	}

    // Add links.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'thsp_plugin_action_links' );

		function thsp_plugin_action_links( $links ) {

		return array_merge(
			array('settings' => '<a href="' . admin_url( 'options-general.php?page=stops-core-theme-and-plugin-updates/Function.php' ) . '">' . __( 'Configure', 'ts-fab' ) . '</a>'),
				$links);
		}

	// Add Files	
	
	// Style.css
		function css() {
            wp_register_style('css', plugins_url('style.css',__FILE__ ));
            wp_enqueue_style('css');
        }
		add_action( 'admin_init','css');
		
	// uninstall.php	
		function php() {
            wp_register_style('php', plugins_url('uninstall.php',__FILE__ ));
            wp_enqueue_style('php');
        }
		add_action( 'admin_init','php');
				
	// lang folder	
		 function action_init() { 
                // Load our textdomain 
                load_plugin_textdomain('stops-core-theme-and-plugin-updates', false , basename(dirname(__FILE__)).'/lang'); 
        } 
?>
