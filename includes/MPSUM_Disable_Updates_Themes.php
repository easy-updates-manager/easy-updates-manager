<?php
/**
 * Disables all WordPress theme updates.
 *
 * Disables all WordPress theme updates.
 *
 * @since 5.0.0
 *
 * @package WordPress
 */
//Credit - From https://wordpress.org/plugins/disable-wordpress-updates/
class MPSUM_Disable_Updates_Themes {
	
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		
		
		/*
		 * Disable Theme Updates
		 * 2.8 to 3.0
		 */
		add_filter( 'pre_transient_update_themes', array( $this, 'last_checked_now' ) );
		/*
		 * 3.0
		 */
		add_filter( 'pre_site_transient_update_themes', array( $this, 'last_checked_now' ) );
		
		/*
		 * Disable All Automatic Updates
		 * 3.7+
		 * 
		 * @author	sLa NGjI's @ slangji.wordpress.com
		 */
		add_filter( 'auto_update_theme', '__return_false' );
		
	} //end constructor
	
	/**
	 * Initialize and load the plugin stuff
	 *
	 * @since 		1.3
	 * @author 		scripts@schloebe.de
	 */
	function admin_init() {
	
		/*
		 * Disable Theme Updates
		 * 2.8 to 3.0
		 */
		remove_action( 'load-themes.php', 'wp_update_themes' );
		remove_action( 'load-update.php', 'wp_update_themes' );
		remove_action( 'admin_init', '_maybe_update_themes' );
		remove_action( 'wp_update_themes', 'wp_update_themes' );
		wp_clear_scheduled_hook( 'wp_update_themes' );
		
		/*
		 * 3.0
		 */
		remove_action( 'load-update-core.php', 'wp_update_themes' );
		wp_clear_scheduled_hook( 'wp_update_themes' );
		
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