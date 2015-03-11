<?php
/*
Plugin Name: Updates Manager
Plugin URI: https://wordpress.org/plugins/stops-core-theme-and-plugin-updates/
Description: Manage WordPress Updates - Works with Multisite
Author: MPS Plugins, kidsguide, ronalfy
Version: 1.0
Requires at least: 4.0
Author URI: https://wordpress.org/plugins/stops-core-theme-and-plugin-updates/
Contributors: MPS Plugins, kidsguide, ronalfy
Text Domain: stops-core-theme-and-plugin-updates
Domain Path: /languages
Updates: true
Network: true
*/ 
class MPSUM_Updates_Manager {
	private static $instance = null;
	private static $options = false;
	
	//Singleton
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	} //end get_instance
	
	private function __construct() {
		//* Localization Code */
		load_plugin_textdomain( 'stops-core-theme-and-plugin-updates', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		spl_autoload_register( array( $this, 'loader' ) );
		
		//Skip disable updates if a user is excluded
		$disable_updates_skip = false;
		if ( current_user_can( 'update_core' ) ) {
			$current_user = wp_get_current_user();
			$current_user_id = $current_user->ID;
			$excluded_users = MPSUM_Updates_Manager::get_options( 'excluded_users' );
			if ( in_array( $current_user_id, $excluded_users ) ) {
				$disable_updates_skip = true;
			}
		}
		if ( false === $disable_updates_skip ) {
			MPSUM_Disable_Updates::run();
		}
		
		
		
		if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
			$skip_admin = defined( 'MPSUM_DISABLE_ADMIN' ) ? (bool)MPSUM_DISABLE_ADMIN : false;
			if ( false === $skip_admin ) {
				MPSUM_Admin::run();	
			}
		}
	} //end constructor

	
	public static function get_plugin_dir( $path = '' ) {
		$dir = rtrim( plugin_dir_path(__FILE__), '/' );
		if ( !empty( $path ) && is_string( $path) )
			$dir .= '/' . ltrim( $path, '/' );
		return $dir;		
	}
	
	//Returns the plugin url
	public static function get_plugin_url( $path = '' ) {
		$dir = rtrim( plugin_dir_url(__FILE__), '/' );
		if ( !empty( $path ) && is_string( $path) )
			$dir .= '/' . ltrim( $path, '/' );
		return $dir;	
	}
	
	public static function get_options( $context = '', $force_reload = false ) {
		//Try to get cached options
		$options = self::$options;
		if ( false === $options || true === $force_reload ) {
			$options = get_site_option( 'MPSUM', false, false );
		}
		
		if ( false === $options ) {
			//todo - do migration script from original plugin	
		}
		
		//Store options
		if ( !is_array( $options ) ) {
			$options = array();	
		}
		self::$options = $options;
		
		//Attempt to get context
		if ( !empty( $context ) && is_string( $context ) ) {
			if ( array_key_exists( $context, $options ) ) {
				return (array)$options[ $context ];	
			} else {
				return array();	
			}
		}
		
		
		return $options;
	} //get_options
	
	private function loader( $class_name ) {
		if ( class_exists( $class_name, false ) || false === strpos( $class_name, 'MPSUM' ) ) {
			return;
		}
		$file = MPSUM_Updates_Manager::get_plugin_dir( "includes/{$class_name}.php" );
		if ( file_exists( $file ) ) {
			include_once( $file );
		}	
	}
	
	public static function update_options( $options = array(), $context = '' ) {
		$options_to_save = self::get_options();
		
		if ( !empty( $context ) && is_string( $context ) ) {
			$options_to_save[ $context ] = $options;
		} else {
			$options_to_save = $options;	
		}
		
		self::$options = $options_to_save;
		update_site_option( 'MPSUM', $options_to_save );
	}
		
} //end class MPSUM_Updates_Manager

add_action( 'plugins_loaded', 'mpsum_instantiate' );
function mpsum_instantiate() {
	MPSUM_Updates_Manager::get_instance();
} //end sce_instantiate