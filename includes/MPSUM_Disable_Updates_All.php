<?php
/**
 * Disables all WordPress updates.
 *
 * Disables all WordPress updates.
 *
 * @since 5.0.0
 *
 * @package WordPress
 */
//Credit - From https://wordpress.org/plugins/disable-wordpress-updates/
class MPSUM_Disable_Updates_All {
	
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
		 * Disable Plugin Updates
		 * 2.8 to 3.0
		 */
		add_action( 'pre_transient_update_plugins', array( $this, 'last_checked_now' ) );
		/*
		 * 3.0
		 */
		add_filter( 'pre_site_transient_update_plugins', array( $this, 'last_checked_now' ) );
		
		
		/*
		 * Disable Core Updates
		 * 2.8 to 3.0
		 */
		add_filter( 'pre_transient_update_core', array( $this, 'last_checked_now' ) );
		/*
		 * 3.0
		 */
		add_filter( 'pre_site_transient_update_core', array( $this, 'last_checked_now' ) );
		

		/*
		 * Disable All Automatic Updates
		 * 3.7+
		 * 
		 * @author	sLa NGjI's @ slangji.wordpress.com
		 */
		add_filter( 'auto_update_translation', '__return_false' );
		add_filter( 'automatic_updater_disabled', '__return_true' );
		add_filter( 'allow_minor_auto_core_updates', '__return_false' );
		add_filter( 'allow_major_auto_core_updates', '__return_false' );
		add_filter( 'allow_dev_auto_core_updates', '__return_false' );
		add_filter( 'auto_update_core', '__return_false' );
		add_filter( 'wp_auto_update_core', '__return_false' );
		add_filter( 'auto_core_update_send_email', '__return_false' );
		add_filter( 'send_core_update_notification_email', '__return_false' );
		add_filter( 'auto_update_plugin', '__return_false' );
		add_filter( 'auto_update_theme', '__return_false' );
		add_filter( 'automatic_updates_send_debug_email', '__return_false' );
		add_filter( 'automatic_updates_is_vcs_checkout', '__return_true' );
		
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
		
		
		/*
		 * Disable Core Updates
		 * 2.8 to 3.0
		 */
		remove_action( 'wp_version_check', 'wp_version_check' );
		remove_action( 'admin_init', '_maybe_update_core' );
		wp_clear_scheduled_hook( 'wp_version_check' );
		
		
		/*
		 * 3.0
		 */
		wp_clear_scheduled_hook( 'wp_version_check' );
		
		
		/*
		 * 3.7+
		 */
		remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
		remove_action( 'admin_init', 'wp_maybe_auto_update' );
		remove_action( 'admin_init', 'wp_auto_update_core' );
		wp_clear_scheduled_hook( 'wp_maybe_auto_update' );
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