<?php
/**
 * Controller class for disabling updates throughout WordPress
 *
 * Controller class for disabling updates throughout WordPress.
 *
 * @since 5.0.0
 *
 * @package WordPress
 */
class MPSUM_Disable_Updates {
	
	/**
	* Holds the class instance.
	*
	* @since 5.0.0
	* @access static
	* @var MPSUM_Disable_Updates $instance
	*/
	private static $instance = null;
	
	/**
	* Set a class instance.
	*
	* Set a class instance.
	*
	* @since 5.0.0 
	* @access static
	*
	*/
	public static function run() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	} //end get_instance	
	
	/**
	* Class constructor.
	*
	* Read in the plugin options and determine which updates are disabled.
	*
	* @since 5.0.0
	* @access private
	*
	*/
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
		
		//Disable Translation Updates
		if ( isset( $core_options[ 'translation_updates' ] ) && 'off' == $core_options[ 'translation_updates' ] ) {
			new MPSUM_Disable_Updates_Translations();
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
		if( isset( $core_options[ 'notification_core_update_emails_plugins' ] ) && 'off' == $core_options[ 'notification_core_update_emails_plugins' ] ) {
    		add_filter( 'send_update_notification_email', array( $this, 'maybe_disable_emails' ), 10, 3 );
        } 
        if( isset( $core_options[ 'notification_core_update_emails_themes' ] ) && 'off' == $core_options[ 'notification_core_update_emails_themes' ] ) {
    		add_filter( 'send_update_notification_email', array( $this, 'maybe_disable_emails' ), 10, 3 );
        }
        if( isset( $core_options[ 'notification_core_update_emails_translations' ] ) && 'off' == $core_options[ 'notification_core_update_emails_translations' ] ) {
    		add_filter( 'send_update_notification_email', array( $this, 'maybe_disable_emails' ), 10, 3 );
        }
		
		
		//Enable Plugin Auto-updates
		if ( isset( $core_options[ 'plugin_updates' ] ) && 'on' == $core_options[ 'plugin_updates' ] ) {
			if ( isset( $core_options[ 'automatic_plugin_updates' ] ) && 'on' == $core_options[ 'automatic_plugin_updates' ] ) {
				add_filter( 'auto_update_plugin',  '__return_true', 50, 2 );
			} elseif( isset( $core_options[ 'automatic_plugin_updates' ] ) && 'off' == $core_options[ 'automatic_plugin_updates' ] ) {
				add_filter( 'auto_update_plugin',  '__return_false', 50, 2 );
			} elseif( isset( $core_options[ 'automatic_plugin_updates' ] ) && 'individual' == $core_options[ 'automatic_plugin_updates' ] ) {
				add_filter( 'auto_update_plugin',  array( $this, 'automatic_updates_plugins' ), 50, 2 );
			}
		}
		
		
		//Enable Theme Auto-updates
		if ( isset( $core_options[ 'theme_updates' ] ) && 'on' == $core_options[ 'theme_updates' ] ) {
			if ( isset( $core_options[ 'automatic_theme_updates' ] ) && 'on' == $core_options[ 'automatic_theme_updates' ] ) {
				add_filter( 'auto_update_theme',  '__return_true', 50, 2 );
			} elseif( isset( $core_options[ 'automatic_theme_updates' ] ) && 'off' == $core_options[ 'automatic_theme_updates' ] ) {
				add_filter( 'auto_update_theme',  '__return_false', 50, 2 );
			} elseif( isset( $core_options[ 'automatic_theme_updates' ] ) && 'individual' == $core_options[ 'automatic_theme_updates' ] ) {
				add_filter( 'auto_update_theme',  array( $this, 'automatic_updates_theme' ), 50, 2 );
			}
		}
		
		//Automatic Updates E-mail Address
		add_filter( 'automatic_updates_debug_email', array( $this, 'maybe_change_automatic_update_email' ), 50 );
		add_filter( 'auto_core_update_email', array( $this, 'maybe_change_automatic_update_email' ), 50 );
		
						
		//Prevent updates on themes/plugins
		add_filter( 'site_transient_update_plugins', array( $this, 'disable_plugin_notifications' ), 50 );
		add_filter( 'site_transient_update_themes', array( $this, 'disable_theme_notifications' ), 50 );
		add_filter( 'http_request_args', array( $this, 'http_request_args_remove_plugins_themes' ), 5, 2 );
		
	} //end constructor
	
	/**
	* Maybe change automatic update email
	*
	* @since 6.1.0
	* @access public
	* @see __construct
	* 
	* @param array $email array
	*
	* @return array email array
	*
	*/
	public function maybe_change_automatic_update_email( $email ) {
		$core_options = MPSUM_Updates_Manager::get_options( 'core' );
		$email_addresses = isset( $core_options[ 'email_addresses' ] ) ? $core_options[ 'email_addresses' ] : array();
		$email_addresses_to_override = array();
		foreach( $email_addresses as $emails ) {
			if ( is_email( $emails ) ) {
				$email_addresses_to_override[] = $emails;
			}
		}
		if ( ! empty( $email_addresses_to_override ) ) {
			$email[ 'to' ] = $email_addresses_to_override;
		}
		return $email;
	}
	
	/**
	* Maybe disable updates.
	*
	* Disable background translation emails, plugin emails, theme emails
	*
	* @since 5.2.0
	* @access public
	* @see __construct
	* 
	* @param bool Whether to disable or not
	* @param type ( theme, plugin , translation )
	* @param obj wp update object
	*
	*/
    public function maybe_disable_emails( $bool, $type, $object ) {
        $core_options = MPSUM_Updates_Manager::get_options( 'core' );
        if( isset( $core_options[ 'notification_core_update_emails_plugins' ] ) && 'off' == $core_options[ 'notification_core_update_emails_plugins' ] && $type == 'plugin' ) {
             return false;
        } 
        if( isset( $core_options[ 'notification_core_update_emails_themes' ] ) && 'off' == $core_options[ 'notification_core_update_emails_themes' ] && $type == 'theme' ) {
             return false;
        } 
        if( isset( $core_options[ 'notification_core_update_emails_translations' ] ) && 'off' == $core_options[ 'notification_core_update_emails_translations' ] && $type == 'translation' ) {
             return false;
        }
        return $bool;
        
    }
	/**
	* Disable the out-of-date browser nag on the WordPress Dashboard.
	*
	* Disable the out-of-date browser nag on the WordPress Dashboard.
	*
	* @since 5.0.0 
	* @access public
	* @see __construct
	* @internal uses wp_dashboard_setup action on single-site, wp_network_dashboard_setup action on multisite
	*
	*/
	public function disable_browser_nag() {
		remove_meta_box( 'dashboard_browser_nag', 'dashboard-network', 'normal' );
		remove_meta_box( 'dashboard_browser_nag', 'dashboard', 'normal' );
	}
	
	/**
	* Enables plugin automatic updates on an individual basis.
	*
	* Enables plugin automatic updates on an individual basis.
	*
	* @since 5.0.0 
	* @access public
	* @see __construct
	* @internal uses auto_update_plugin filter
	*
	* @param bool $update Whether the item has automatic updates enabled
	* @param object $item Object holding the asset to be updated
	* @return bool True of automatic updates enabled, false if not
	*/
	public function automatic_updates_plugins( $update, $item ) {
		$plugin_automatic_options = MPSUM_Updates_Manager::get_options( 'plugins_automatic' );
		if ( in_array( $item->plugin, $plugin_automatic_options ) ) {
			return true;
		}
		return false;
	}
	
	/**
	* Enables theme automatic updates on an individual basis.
	*
	* Enables theme automatic updates on an individual basis.
	*
	* @since 5.0.0 
	* @access public
	* @see __construct
	* @internal uses auto_update_theme filter
	*
	* @param bool $update Whether the item has automatic updates enabled
	* @param object $item Object holding the asset to be updated
	* @return bool True of automatic updates enabled, false if not
	*/
	public function automatic_updates_theme( $update, $item ) {
		$theme_automatic_options = MPSUM_Updates_Manager::get_options( 'themes_automatic' );
		if ( in_array( $item->theme , $theme_automatic_options) ) {
			return true;
		}
		return false;
	}
	
	/**
	* Disables plugin updates on an individual basis.
	*
	* Disables plugin updates on an individual basis.
	*
	* @since 5.0.0 
	* @access public
	* @see __construct
	* @internal uses site_transient_update_plugins filter
	*
	* @param object $plugins Plugins that may have update notifications
	* @return object Updated plugins list with updates
	*/
	public function disable_plugin_notifications( $plugins ) {
		if ( !isset( $plugins->response ) || empty( $plugins->response ) ) return $plugins;
		
		$plugin_options = MPSUM_Updates_Manager::get_options( 'plugins' );
		foreach( $plugin_options as $plugin ) {
			unset( $plugins->response[ $plugin ] );
		}
		return $plugins;
	}
	
	/**
	* Disables theme updates on an individual basis.
	*
	* Disables theme updates on an individual basis.
	*
	* @since 5.0.0 
	* @access public
	* @see __construct
	* @internal uses site_transient_update_themes filter
	*
	* @param object $themes Themes that may have update notifications
	* @return object Updated themes list with updates
	*/
	public function disable_theme_notifications( $themes ) {
		if ( !isset( $themes->response ) || empty( $themes->response ) ) return $themes;
		
		$theme_options = MPSUM_Updates_Manager::get_options( 'themes' );
		foreach( $theme_options as $theme ) {
			unset( $themes->response[ $theme ] );
		}
		return $themes;
	}
	
	/**
	* Disables theme and plugin http requests on an individual basis.
	*
	* Disables theme and plugin http requests on an individual basis.
	*
	* @since 5.0.0 
	* @access public
	* @see __construct
	* @internal uses http_request_args filter
	*
	* @param array $r Request array
	* @param string $url URL requested
	* @return array Updated Request array
	*/
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