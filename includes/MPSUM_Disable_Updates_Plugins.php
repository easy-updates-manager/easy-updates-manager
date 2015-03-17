<?php
/**
 * Disables all WordPress plugin updates.
 *
 * Disables all WordPress plugin updates.
 *
 * @since 5.0.0
 *
 * @package WordPress
 */
//Credit - From https://wordpress.org/plugins/disable-wordpress-updates/
class MPSUM_Disable_Updates_Plugins {
	
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		
		/*
		 * Disable Plugin Updates
		 * 2.8 to 3.0
		 */
		add_action( 'pre_transient_update_plugins', array( $this, 'last_checked_now' ) );
		/*
		 * 3.0
		 */
		add_filter( 'pre_site_transient_update_plugins', array( $this, 'last_checked_now' ) );
		
		/*
		 * Disable All Automatic Updates
		 * 3.7+
		 * 
		 * @author	sLa NGjI's @ slangji.wordpress.com
		 */
		add_filter( 'auto_update_plugin', '__return_false' );
		
	} //end constructor
	
	/**
	 * Initialize and load the plugin stuff
	 *
	 * @since 		1.3
	 * @author 		scripts@schloebe.de
	 */
	function admin_init() {
		
		
		/*
		 * Disable Plugin Updates
		 * 2.8 to 3.0
		 */
		remove_action( 'load-plugins.php', 'wp_update_plugins' );
		remove_action( 'load-update.php', 'wp_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'wp_update_plugins', 'wp_update_plugins' );
		wp_clear_scheduled_hook( 'wp_update_plugins' );
		
		/*
		 * 3.0
		 */
		remove_action( 'load-update-core.php', 'wp_update_plugins' );
		wp_clear_scheduled_hook( 'wp_update_plugins' );
		
	}
	
	public function last_checked_now( $transient ) {
		include ABSPATH . WPINC . '/version.php';
		$current = new stdClass;
		$current->updates = array();
		$current->version_checked = $wp_version;
		$current->last_checked = time();
		
		return $current;
	}
	
}