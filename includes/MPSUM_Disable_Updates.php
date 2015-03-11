<?php
class MPSUM_Disable_Updates {
	private static $instance = null;
	
	//Singleton
	public static function run() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	} //end get_instance	
	
	private function __construct() {
				
		$core_options = MPSUM_Updates_Manager::get_options( 'core' );
		
		//Disable Footer Nag
		if ( isset( $core_options[ 'misc_wp_footer' ] ) && 'off' === $core_options[ 'misc_wp_footer' ] ) {
			add_filter( 'update_footer', '__return_empty_string', 11 );
		}
		
		//Disable Browser Nag
		if ( isset( $core_options[ 'misc_browser_nag' ] ) && 'off' === $core_options[ 'misc_browser_nag' ] ) {
			add_action( 'wp_dashboard_setup', array( $this, 'disable_browser_nag' ), 9 );
			add_action( 'wp_network_dashboard_setup', array( $this, 'disable_browser_nag' ), 9 );
		}
		
		//Disable All Updates
		if ( isset( $core_options[ 'all_updates' ] ) && 'off' == $core_options[ 'all_updates' ] ) {
			new MPSUM_Disable_Updates_All();
			return;	
		}
		
		//Disable WordPress Updates
		if ( isset( $core_options[ 'core_updates' ] ) && 'off' == $core_options[ 'core_updates' ] ) {
			new MPSUM_Disable_Updates_WordPress();
		}
		
		//Disable Plugin Updates
		if ( isset( $core_options[ 'plugin_updates' ] ) && 'off' == $core_options[ 'plugin_updates' ] ) {
			new MPSUM_Disable_Updates_Plugins();
		}
		
		//Disable Theme Updates
		if ( isset( $core_options[ 'theme_updates' ] ) && 'off' == $core_options[ 'theme_updates' ] ) {
			new MPSUM_Disable_Updates_Themes();
		}
		
		//Enable Development Updates
		if ( isset( $core_options[ 'automatic_development_updates' ] ) && 'on' == $core_options[ 'automatic_development_updates' ] ) {
			add_filter( 'allow_dev_auto_core_updates', '__return_true', 50 );
		} elseif( isset( $core_options[ 'automatic_development_updates' ] ) && 'off' == $core_options[ 'automatic_development_updates' ] ) {
			add_filter( 'allow_dev_auto_core_updates', '__return_false', 50 );
		}
		
		//Enable Core Major Updates
		if ( isset( $core_options[ 'automatic_major_updates' ] ) && 'on' == $core_options[ 'automatic_major_updates' ] ) {
			add_filter( 'allow_major_auto_core_updates', '__return_true', 50 );
		} elseif( isset( $core_options[ 'automatic_major_updates' ] ) && 'off' == $core_options[ 'automatic_major_updates' ] ) {
			add_filter( 'allow_major_auto_core_updates', '__return_false', 50 );
		}
		
		//Enable Core Minor Updates
		if ( isset( $core_options[ 'automatic_minor_updates' ] ) && 'on' == $core_options[ 'automatic_minor_updates' ] ) {
			add_filter( 'allow_minor_auto_core_updates', '__return_true', 50 );
		} elseif( isset( $core_options[ 'automatic_minor_updates' ] ) && 'off' == $core_options[ 'automatic_minor_updates' ] ) {
			add_filter( 'allow_minor_auto_core_updates', '__return_false', 50 );
		}
		
		//Enable Translation Updates
		if ( isset( $core_options[ 'automatic_translation_updates' ] ) && 'on' == $core_options[ 'automatic_translation_updates' ] ) {
			add_filter( 'auto_update_translation', '__return_true', 50 );
		} elseif( isset( $core_options[ 'automatic_translation_updates' ] ) && 'off' == $core_options[ 'automatic_translation_updates' ] ) {
			add_filter( 'auto_update_translation', '__return_false', 50 );
		}
		
		//Disable the Update Notification
		if ( isset( $core_options[ 'notification_core_update_emails' ] ) && 'on' == $core_options[ 'notification_core_update_emails' ] ) {
			add_filter( 'auto_core_update_send_email', '__return_true', 50 );
			add_filter( 'send_core_update_notification_email', '__return_true', 50 );
			add_filter( 'automatic_updates_send_debug_email', '__return_true', 50 );
		} elseif( isset( $core_options[ 'notification_core_update_emails' ] ) && 'off' == $core_options[ 'notification_core_update_emails' ] ) {
			add_filter( 'auto_core_update_send_email', '__return_false', 50 );
			add_filter( 'send_core_update_notification_email', '__return_false', 50 );
			add_filter( 'automatic_updates_send_debug_email', '__return_false', 50 );
		}
		
		//Enable Plugin Auto-updates
		if ( isset( $core_options[ 'plugin_updates' ] ) && 'on' == $core_options[ 'plugin_updates' ] ) {
			if ( isset( $core_options[ 'automatic_plugin_updates' ] ) && 'on' == $core_options[ 'automatic_plugin_updates' ] ) {
				add_filter( 'auto_update_plugin',  '__return_true', 10, 2 );
			} elseif( isset( $core_options[ 'automatic_plugin_updates' ] ) && 'off' == $core_options[ 'automatic_plugin_updates' ] ) {
				add_filter( 'auto_update_plugin',  '__return_false', 10, 2 );
			} elseif( isset( $core_options[ 'automatic_plugin_updates' ] ) && 'individual' == $core_options[ 'automatic_plugin_updates' ] ) {
				add_filter( 'auto_update_plugin',  array( $this, 'automatic_updates_plugins' ), 10, 2 );
			}
		}
		
		
		//Enable Theme Auto-updates
		if ( isset( $core_options[ 'theme_updates' ] ) && 'on' == $core_options[ 'theme_updates' ] ) {
			if ( isset( $core_options[ 'automatic_theme_updates' ] ) && 'on' == $core_options[ 'automatic_theme_updates' ] ) {
				add_filter( 'auto_update_theme',  '__return_true', 10, 2 );
			} elseif( isset( $core_options[ 'automatic_theme_updates' ] ) && 'off' == $core_options[ 'automatic_theme_updates' ] ) {
				add_filter( 'auto_update_theme',  '__return_false', 10, 2 );
			} elseif( isset( $core_options[ 'automatic_theme_updates' ] ) && 'individual' == $core_options[ 'automatic_theme_updates' ] ) {
				add_filter( 'auto_update_theme',  array( $this, 'automatic_updates_theme' ), 10, 2 );
			}
		}
		
						
		//Prevent updates on themes/plugins
		add_filter( 'site_transient_update_plugins', array( $this, 'disable_plugin_notifications' ), 50 );
		add_filter( 'site_transient_update_themes', array( $this, 'disable_theme_notifications' ), 50 );
		add_filter( 'http_request_args', array( $this, 'http_request_args_remove_plugins_themes' ), 5, 2 );
		
	} //end constructor
	
	public function disable_browser_nag() {
		remove_meta_box( 'dashboard_browser_nag', 'dashboard-network', 'normal' );
		remove_meta_box( 'dashboard_browser_nag', 'dashboard', 'normal' );
	}
	
	public function automatic_updates_plugins( $update, $item ) {
		$plugin_automatic_options = MPSUM_Updates_Manager::get_options( 'plugins_automatic' );
		if ( in_array( $item->plugin, $plugin_automatic_options ) ) {
			return true;
		}
		return false;
	}
	
	public function automatic_updates_theme( $update, $item ) {
		$theme_automatic_options = MPSUM_Updates_Manager::get_options( 'themes_automatic' );
		if ( in_array( $item->theme , $theme_automatic_options) ) {
			return true;
		}
		return false;
	}
	
	public function disable_plugin_notifications( $plugins ) {
		if ( !isset( $plugins->response ) || empty( $plugins->response ) ) return $plugins;
		
		$plugin_options = MPSUM_Updates_Manager::get_options( 'plugins' );
		foreach( $plugin_options as $plugin ) {
			unset( $plugins->response[ $plugin ] );
		}
		return $plugins;
	}
	
	public function disable_theme_notifications( $themes ) {
		if ( !isset( $themes->response ) || empty( $themes->response ) ) return $themes;
		
		$theme_options = MPSUM_Updates_Manager::get_options( 'themes' );
		foreach( $theme_options as $theme ) {
			unset( $themes->response[ $theme ] );
		}
		return $themes;
	}
	
	public function http_request_args_remove_plugins_themes( $r, $url ) {
		if ( 0 !== strpos( $url, 'https://api.wordpress.org/plugins/update-check/1.1/' ) ) return $r;
		
		if ( isset( $r[ 'body' ][ 'plugins' ] ) ) {
			$r_plugins = json_decode( $r[ 'body' ][ 'plugins' ], true );
			$plugin_options = MPSUM_Updates_Manager::get_options( 'plugins' );
			foreach( $plugin_options as $plugin ) {
				unset( $r_plugins[ $plugin ] );
				if ( false !== $key = array_search( $plugin, $r_plugins[ 'active' ] ) ) {
					unset( $r_plugins[ 'active' ][ $key ] );
					$r_plugins[ 'active' ] = array_values( $r_plugins[ 'active' ] );
				}
			}
			$r[ 'body' ][ 'plugins' ] = json_encode( $r_plugins );
		}
		if ( isset( $r[ 'body' ][ 'themes' ] ) ) {
			$r_themes = json_decode( $r[ 'body' ][ 'themes' ], true );
			$theme_options = MPSUM_Updates_Manager::get_options( 'themes' );
			foreach( $theme_options as $theme ) {
				unset( $r_themes[ $theme ] );
			}
			$r[ 'body' ][ 'themes' ] = json_encode( $r_themes );
		}
		return $r;
	}
	
}