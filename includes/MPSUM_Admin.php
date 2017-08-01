<?php
/**
 * Easy Updates Manager admin controller.
 *
 * Initializes the admin panel options and load the admin dependencies
 *
 * @since 5.0.0
 *
 * @package WordPress
 */
class MPSUM_Admin {
	
	/**
	* Holds the class instance.
	*
	* @since 5.0.0
	* @access static
	* @var MPSUM_Admin $instance
	*/
	private static $instance = null;
	
	/**
	* Holds the URL to the admin panel page
	*
	* @since 5.0.0
	* @access static
	* @var string $url
	*/
	private static $url = '';
	
	/**
	* Holds the slug to the admin panel page
	*
	* @since 5.0.0
	* @access static
	* @var string $slug
	*/
	private static $slug = 'mpsum-update-options';
	
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
		return self::$instance;
	} //end get_instance	
	
	/**
	* Class constructor.
	*
	* Initialize the class
	*
	* @since 5.0.0
	* @access private
	*
	*/
	private function __construct() {
		add_action( 'init', array( $this, 'init' ), 9 );
		add_filter( 'set-screen-option', array( $this, 'add_screen_option_save' ), 10, 3 );
	} //end constructor
	
	/**
	* Save the screen options.
	*
	* Save the screen options.
	*
	* @since 6.2.0 
	* @access static
	*
	* @return string URL to the admin panel page.
	*/
	public function add_screen_option_save( $status, $option, $value ) {
		return MPSUM_Admin_Screen_Options::save_options( $status, $option, $value );
	}
	
	/**
	* Return the URL to the admin panel page.
	*
	* Return the URL to the admin panel page.
	*
	* @since 5.0.0 
	* @access static
	*
	* @return string URL to the admin panel page.
	*/
	public static function get_url() {
		$url = self::$url;
		if ( empty( $url ) ) {
			if ( is_multisite() ) {
				$url = add_query_arg( array( 'page' => self::get_slug() ), network_admin_url( 'index.php' ) );	
			} else {
				$url = add_query_arg( array( 'page' => self::get_slug() ), admin_url( 'index.php' ) );
			}
			self::$url = $url;
		}
		return $url;
	}
	
	/**
	* Return the slug for the admin panel page.
	*
	* Return the slug for the admin panel page.
	*
	* @since 5.0.0 
	* @access static
	*
	* @return string slug to the admin panel page.
	*/
	public static function get_slug() {
		return self::$slug;
	}
	
	/**
	* Initialize the admin menu.
	*
	* Initialize the admin menu.
	*
	* @since 5.0.0 
	* @access public
	* @see __construct
	* @internal Uses init action
	*
	*/
	public function init() {
		
		//Plugin and Theme actions
		if ( is_multisite() ) {
			add_action( 'network_admin_menu', array( $this, 'init_network_admin_menus' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'init_single_site_admin_menus' ) );
		}
		
		//Add settings link to plugins screen
		$prefix = is_multisite() ? 'network_admin_' : '';
		add_action( $prefix . 'plugin_action_links_' . MPSUM_Updates_Manager::get_plugin_basename(), array( $this, 'plugin_settings_link' ) );
		
		//todo - maybe load these conditionally based on $_REQUEST[ 'tab' ] param
		$core_options = MPSUM_Updates_Manager::get_options( 'core' );
		new MPSUM_Admin_Dashboard( self::get_slug() );
		new MPSUM_Admin_Plugins( self::get_slug() );
		new MPSUM_Admin_Themes( self::get_slug() );
		if ( isset( $core_options[ 'logs' ] ) && 'on' == $core_options[ 'logs' ] ) {
    		new MPSUM_Admin_Logs( self::get_slug() );
		}
		new MPSUM_Admin_Core( self::get_slug() );
		new MPSUM_Admin_Advanced( self::get_slug() );
		
		MPSUM_Admin_Screen_Options::maybe_save_dashboard_screen_option();
		
	}	
	
	/**
	* Initializes the help screen.
	*
	* Initializes the help screen.
	*
	* @since 5.0.0 
	* @access public
	* @see init
	* @internal Uses load_{$hook} action
	*
	*/
	public function init_help_screen() {
		new MPSUM_Admin_Help();
	}
	
	/**
	* Initializes the screen options.
	*
	* Initializes the screen options.
	*
	* @since 6.2
	* @access public
	* @see init
	* @internal Uses load_{$hook} action
	*
	*/
	public function init_screen_options() {
		MPSUM_Admin_Screen_Options::run();
	}
	
	/**
	 * Returns formatted array of EUM options
	 *
	 * Returns formatted array of EUM options.
	 *
	 * @since 6.3
	 * @access public
	 *
	 * @return array EUM implementation options
	 *
	 */
	private function get_options_for_default() {
		$options = MPSUM_Updates_Manager::get_options();
		if ( ! isset( $options[ 'core' ] ) ) {
			$options[ 'core' ] = MPSUM_Admin_Core::get_defaults();
		}
		if ( ! isset( $options[ 'plugins' ] ) ) {
			$options[ 'plugins' ] = array();	
		}
		if ( ! isset( $options[ 'themes' ] ) ) {
			$options[ 'themes' ] = array();	
		}
		if ( ! isset( $options[ 'plugins_automatic' ] ) ) {
			$options[ 'plugins_automatic' ] = array();	
		}
		if ( ! isset( $options[ 'themes_automatic' ] ) ) {
			$options[ 'themes_automatic' ] = array();	
		}
		return $options;
		
	}
	
	/**
	 * Returns whether a JSON component should be disabled or not.
	 *
	 * Returns whether a JSON component should be disabled or not.
	 *
	 * @since 6.3
	 * @access public
	 *
	 * @param string $option - Option to check
	 * @param string $context - Option context
	 * @return bool True of disabled, false if not
	 *
	 */
	private function get_json_maybe_disabled( $option, $context = 'core' ) {
		$options = $this->get_options_for_default();
		if ( 'off' == $options[ 'core' ][ 'all_updates' ] && 'all_updates' != $option ) {
			// $context disables an entire section
			switch( $context ) {
				case 'plugins':
				case 'themes':
				case 'plugins_automatic':
				case 'themes_automatic':
				case 'core':
					return true;
					break;
			}
				
		} elseif ( 'off' == $options[ 'core' ][ 'plugin_updates' ] && 'plugin_updates' !== $option ) {
			switch( $context ) {
				case 'plugins':
				case 'plugins_automatic':
					return true;
					break;
			}
			switch( $option ) {
				case 'automatic_plugin_updates':
					return true;
					break;
			}
		} elseif( 'off' == $options[ 'core' ][ 'core_updates' ] && 'core_updates' != $option ) {
			switch( $option ) {
				case 'automatic_major_updates':
				case 'automatic_minor_updates':
				case 'automatic_development_updates':
				case 'automatic_plugin_updates':
				case 'automatic_theme_updates':
				case 'automatic_translation_updates':
					return true;
					break;
			}
			switch( $context ) {
				case 'plugins_automatic':
				case 'themes_automatic':
					return true;
					break;
			}
		
		} elseif ( 'off' == $options[ 'core' ][ 'translation_updates' ] && 'translation_updates' != $option ) {
					

			switch( $option ) {
				case 'automatic_translation_updates':
					return true;
					break;
			}
		} elseif ( 'off' == $options[ 'core' ][ 'theme_updates' ] && 'theme_updates' != $option ) {
			switch( $context ) {
				case 'themes':
				case 'themes_automatic':
					return true;
					break;
			}
			switch( $option ) {
				case 'automatic_theme_updates':
					return true;
					break;
			}
		}
		
		switch( $context ) {
			case 'plugins_automatic':
				if ( isset( $options[ 'core' ][ 'automatic_plugin_updates' ] ) && 'on' == $options[ 'core' ][ 'automatic_plugin_updates' ] ) {
					return true;
				} else if ( isset( $options[ 'core' ][ 'automatic_plugin_updates' ] ) && 'off' == $options[ 'core' ][ 'automatic_plugin_updates' ] ) {
					return true;
				} else if ( isset( $options[ 'core' ][ 'automatic_plugin_updates' ] ) && 'default' == $options[ 'core' ][ 'automatic_plugin_updates' ] ) {
					return true;
				} else if ( isset( $options[ 'core' ][ 'automatic_plugin_updates' ] ) && 'individual' == $options[ 'core' ][ 'automatic_plugin_updates' ] ) {
					if ( false !== array_search( $option, $options[ 'plugins' ] ) ) {
						return true;
					}
					return false;
				}
				break;
			case 'themes_automatic':
				if ( isset( $options[ 'core' ][ 'automatic_theme_updates' ] ) && 'on' == $options[ 'core' ][ 'automatic_theme_updates' ] ) {
					return true;
				} else if ( isset( $options[ 'core' ][ 'automatic_theme_updates' ] ) && 'off' == $options[ 'core' ][ 'automatic_theme_updates' ] ) {
					return true;
				} else if ( isset( $options[ 'core' ][ 'automatic_theme_updates' ] ) && 'default' == $options[ 'core' ][ 'automatic_theme_updates' ] ) {
					return true;
				} else if ( isset( $options[ 'core' ][ 'automatic_theme_updates' ] ) && 'individual' == $options[ 'core' ][ 'automatic_theme_updates' ] ) {
					if ( false !== array_search( $option, $options[ 'themes' ] ) ) {
						return true;
					}
					return false;
				}
				break;
		}
		return false;
	}
	
	/**
	 * Returns whether a JSON component should be enabled or not.
	 *
	 * Returns whether a JSON component should be enabled or not.
	 *
	 * @since 6.3
	 * @access public
	 *
	 * @param string $option - Option to check
	 * @param string $context - Option context
	 * @return bool True of enabled, false if not
	 *
	 */
	private function get_json_maybe_checked( $option, $context = 'core' ) {
		$options = $this->get_options_for_default();
		if ( 'off' == $options[ 'core' ][ 'all_updates' ] && 'all_updates' != $option ) {
			// $context disables an entire section
			switch( $context ) {
				case 'plugins':
				case 'themes':
				case 'plugins_automatic':
				case 'themes_automatic':
				case 'core':
					return false;
					break;
			}	
		} elseif ( 'off' == $options[ 'core' ][ 'translation_updates' ] && 'translation_updates' != $option ) {
			switch( $option ) {
				case 'automatic_translation_updates':
					return false;
					break;
			}
		} elseif( 'off' == $options[ 'core' ][ 'core_updates' ] && 'core_updates' != $option ) {
			switch( $option ) {
				case 'automatic_major_updates':
				case 'automatic_minor_updates':
				case 'automatic_development_updates':
				case 'automatic_plugin_updates':
				case 'automatic_theme_updates':
				case 'automatic_translation_updates':
					return false;
					break;
			}
			switch( $context ) {
				case 'plugins_automatic':
				case 'themes_automatic':
					return false;
					break;
			}
		
		} elseif ( 'off' == $options[ 'core' ][ 'theme_updates' ] && 'theme_updates' != $option ) {
			switch( $context ) {
				case 'themes':
				case 'themes_automatic':
					return false;
					break;
			}
		} elseif ( 'off' == $options[ 'core' ][ 'plugin_updates' ] && 'plugin_updates' != $option ) {
			switch( $context ) {
				case 'plugins':
				case 'plugins_automatic':
					return false;
					break;
			}
		}
		
		if( isset( $options[ $context ][ $option ] ) && 'on' == $options[ $context ][ $option ] ) {
			return true;	
		} else {
			switch( $context ) {
				case 'plugins':
					$option_search = array_search( $option, $options[ $context ] );
					if ( false === $option_search ) {
						return true;
					} else {
						return false;
					}
					break;
				case 'themes':
					$option_search = array_search( $option, $options[ $context ] );
					if ( false === $option_search ) {
						return true;
					} else {
						return false;
					}
					break;
				case 'plugins_automatic':
					if ( isset( $options[ 'core' ][ 'automatic_plugin_updates' ] ) && 'on' == $options[ 'core' ][ 'automatic_plugin_updates' ] ) {
						return true;
					} else if ( isset( $options[ 'core' ][ 'automatic_plugin_updates' ] ) && 'default' == $options[ 'core' ][ 'automatic_plugin_updates' ] ) {
						return false;
					} else if ( isset( $options[ 'core' ][ 'automatic_plugin_updates' ] ) && 'individual' == $options[ 'core' ][ 'automatic_plugin_updates' ] ) {
						$option_search = array_search( $option, $options[ $context ] );
						if ( false === $option_search  || ( false !== array_search( $option, $options[ 'plugins' ] ) ) ) {
							return false;
						} else {
							return true;
						}
					}
					break;
				case 'themes_automatic':
					if ( isset( $options[ 'core' ][ 'automatic_theme_updates' ] ) && 'on' == $options[ 'core' ][ 'automatic_theme_updates' ] ) {
						return true;
					} else if ( isset( $options[ 'core' ][ 'automatic_theme_updates' ] ) && 'default' == $options[ 'core' ][ 'automatic_theme_updates' ] ) {
						return false;
					} else if ( isset( $options[ 'core' ][ 'automatic_theme_updates' ] ) && 'individual' == $options[ 'core' ][ 'automatic_theme_updates' ] ) {
						$option_search = array_search( $option, $options[ $context ] );
						if ( false === $option_search || ( false !== array_search( $option, $options[ 'themes' ] ) ) ) {
							return false;
						} else {
							return true;
						}
					}
					break;
			}
			return false;
		}
		return false;
	}
	
	/**
	 * Returns whether a JSON component should be selected or not.
	 *
	 * Returns whether a JSON component should be selected or not.
	 *
	 * @since 6.3
	 * @access public
	 *
	 * @param string $option - Option to check
	 * @param string $context - Option context
	 * @return string value of selected index
	 *
	 */
	private function get_json_maybe_selected( $option, $context = 'core' ) {
		$options = $this->get_options_for_default();
		if ( 'off' == $options[ 'core' ][ 'all_updates' ] && 'all_updates' != $option ) {
			switch( $option ) {
				case 'automatic_plugin_updates':
				case 'automatic_theme_updates':
					return 'off';
					break;
				default:
					break;
			}	
		} elseif ( 'off' == $options[ 'core' ][ 'plugin_updates' ] && 'plugin_updates' != $option ) {
			switch( $option ) {
				case 'automatic_plugin_updates':
					return 'off';
					break;
			}
		} elseif ( 'off' == $options[ 'core' ][ 'theme_updates' ] && 'theme_updates' != $option ) {
			switch( $option ) {
				case 'automatic_theme_updates':
					return 'off';
					break;
			}
		} elseif( 'off' == $options[ 'core' ][ 'core_updates' ] && 'core_updates' != $option ) {
			switch( $option ) {
				case 'automatic_theme_updates':
				case 'automatic_plugin_updates':
					return 'off';
					break;
			}	
		}
		
		return $options[ $context ][ $option ];
	}
	
	/**
	 * Returns JSON options for use in React
	 *
	 * Returns JSON options for use in React.
	 *
	 * @since 6.3
	 * @access public
	 */
	public function get_json_options() {
		$options = MPSUM_Updates_Manager::get_options();
		
		$boxes = array();
		$boxes[] = array(
			'title' => __( 'WordPress Updates', 'stops-core-theme-and-plugin-updates' ),
			'component' => 'ToggleWrapper',
			'items' => array(
				array(
					'component' => 'ToggleItem',
					'title' => __( 'All Updates', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'all_updates',
					'disabled' => $this->get_json_maybe_disabled( 'all_updates' ),
					'checked' => $this->get_json_maybe_checked( 'all_updates' ),
					'loading' => false,
					'context' => 'core'
				),
				array(
					'component' => 'ToggleItem',
					'title' => __( 'WordPress Core Updates', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'core_updates',
					'disabled' => $this->get_json_maybe_disabled( 'core_updates' ),
					'checked' => $this->get_json_maybe_checked( 'core_updates' ),
					'loading' => false,
					'context' => 'core'
				),
				array(
					'component' => 'ToggleItem',
					'title' => __( 'All Plugin Updates', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'plugin_updates',
					'disabled' => $this->get_json_maybe_disabled( 'plugin_updates' ),
					'checked' => $this->get_json_maybe_checked( 'plugin_updates' ),
					'loading' => false,
					'context' => 'core'
				),
				array(
					'component' => 'ToggleItem',
					'title' => __( 'All Theme Updates', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'theme_updates',
					'disabled' => $this->get_json_maybe_disabled( 'theme_updates' ),
					'checked' => $this->get_json_maybe_checked( 'theme_updates' ),
					'loading' => false,
					'context' => 'core'
				),
				array(
					'component' => 'ToggleItem',
					'title' => __( 'All Translation Updates', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'translation_updates',
					'disabled' => $this->get_json_maybe_disabled( 'translation_updates' ),
					'checked' => $this->get_json_maybe_checked( 'translation_updates' ),
					'loading' => false,
					'context' => 'core'
				)
				
			),
		);
		$boxes[] = array(
			'title' => __( 'Automatic Updates', 'stops-core-theme-and-plugin-updates' ),
			'items' => array(
				array(
					'component' => 'ToggleItem',
					'title' => __( 'Major Releases', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'automatic_major_updates',
					'disabled' => $this->get_json_maybe_disabled( 'automatic_major_updates' ),
					'checked' => $this->get_json_maybe_checked( 'automatic_major_updates' ),
					'loading' => false,
					'context' => 'core'
				),
				array(
					'component' => 'ToggleItem',
					'title' => __( 'Minor Releases', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'automatic_minor_updates',
					'disabled' => $this->get_json_maybe_disabled( 'automatic_minor_updates' ),
					'checked' => $this->get_json_maybe_checked( 'automatic_minor_updates' ),
					'loading' => false,
					'context' => 'core'
				),
				array(
					'component' => 'ToggleItem',
					'title' => __( 'Development Updates', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'automatic_development_updates',
					'disabled' => $this->get_json_maybe_disabled( 'automatic_development_updates' ),
					'checked' => $this->get_json_maybe_checked( 'automatic_development_updates' ),
					'loading' => false,
					'context' => 'core'
				),
				array(
					'component' => 'ToggleItem',
					'title' => __( 'Translation Updates', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'automatic_translation_updates',
					'disabled' => $this->get_json_maybe_disabled( 'automatic_translation_updates' ),
					'checked' => $this->get_json_maybe_checked( 'automatic_translation_updates' ),
					'loading' => false,
					'context' => 'core'
				),
				array(
					'component' => 'ToggleItemRadio',
					'title' => __( 'Automatic Plugin Updates', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'automatic_plugin_updates',
					'disabled' => $this->get_json_maybe_disabled( 'automatic_plugin_updates' ),
					'checked' => $this->get_json_maybe_selected( 'automatic_plugin_updates' ),
					'loading' => false,
					'context' => 'core',
					'choices' => array(
						array(
							'id'    => 'automatic_plugin_on',
							'label' => __( 'Enabled', 'stops-core-theme-and-plugin-updates' ),
							'value' => 'on'
						),
						array(
							'id'    => 'automatic_plugin_off',
							'label' => __( 'Disabled', 'stops-core-theme-and-plugin-updates' ),
							'value' => 'off'
						),
						array(
							'id'    => 'automatic_plugin_default',
							'label' => __( 'Default', 'stops-core-theme-and-plugin-updates' ),
							'value' => 'default'
						),
						array(
							'id'    => 'automatic_plugin_individual',
							'label' => __( 'Select Individually', 'stops-core-theme-and-plugin-updates' ),
							'value' => 'individual'
						)
					)
				),
				array(
					'component' => 'ToggleItemRadio',
					'title' => __( 'Automatic Theme Updates', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'automatic_theme_updates',
					'disabled' => $this->get_json_maybe_disabled( 'automatic_theme_updates' ),
					'checked' => $this->get_json_maybe_selected( 'automatic_theme_updates' ),
					'loading' => false,
					'context' => 'core',
					'choices' => array(
						array(
							'id'    => 'automatic_theme_on',
							'label' => __( 'Enabled', 'stops-core-theme-and-plugin-updates' ),
							'value' => 'on'
						),
						array(
							'id'    => 'automatic_theme_off',
							'label' => __( 'Disabled', 'stops-core-theme-and-plugin-updates' ),
							'value' => 'off'
						),
						array(
							'id'    => 'automatic_theme_default',
							'label' => __( 'Default', 'stops-core-theme-and-plugin-updates' ),
							'value' => 'default'
						),
						array(
							'id'    => 'automatic_theme_individual',
							'label' => __( 'Select Individually', 'stops-core-theme-and-plugin-updates' ),
							'value' => 'individual'
						)
					)
				)
			)
		);
		$plugins = get_plugins();
		$plugin_items = array(); 
		$plugin_automatic_items = array();
		foreach( $plugins as $plugin_slug => $plugin_data ) {
			$plugin_items[] = array(
				'component' => 'ToggleItem',
				'title' => $plugin_data[ 'Name' ],
				'id' => $plugin_slug,
				'name' => 'plugins',
				'disabled' => $this->get_json_maybe_disabled( $plugin_slug, 'plugins' ),
				'checked' => $this->get_json_maybe_checked( $plugin_slug, 'plugins' ),
				'loading' => false,
				'context' => 'plugins',
			);
			$plugin_automatic_items[] = array(
				'component' => 'ToggleItem',
				'title' => $plugin_data[ 'Name' ],
				'id' => $plugin_slug,
				'name' => 'plugins',
				'disabled' => $this->get_json_maybe_disabled( $plugin_slug, 'plugins_automatic' ),
				'checked' => $this->get_json_maybe_checked( $plugin_slug, 'plugins_automatic' ),
				'loading' => false,
				'context' => 'plugins_automatic',
			); 
		}
		$themes = wp_get_themes();
		$theme_items = array(); 
		$theme_automatic_items = array();
		foreach( $themes as $theme_slug => $theme_data ) {
			$theme_items[] = array(
				'component' => 'ToggleItem',
				'title' => $theme_data->Name,
				'id' => $theme_slug,
				'name' => 'themes',
				'disabled' => $this->get_json_maybe_disabled( $theme_slug, 'themes' ),
				'checked' => $this->get_json_maybe_checked( $theme_slug, 'themes' ),
				'loading' => false,
				'context' => 'themes',
			);
			$theme_automatic_items[] = array(
				'component' => 'ToggleItem',
				'title' => $theme_data->Name,
				'id' => $theme_slug,
				'name' => 'themes',
				'disabled' => $this->get_json_maybe_disabled( $theme_slug, 'themes_automatic' ),
				'checked' => $this->get_json_maybe_checked( $theme_slug, 'themes_automatic' ),
				'loading' => false,
				'context' => 'themes_automatic',
			);
		}
		$boxes[] = array(
			'title' => __( 'Plugin and Theme Updates', 'stops-core-theme-and-plugin-updates' ),
			'items' => array( 
				array(
					'id'        => 'plugins-themes',
					'component' => 'ToggleTabs',
					'active'    => 'plugin-updates',
					'tabs' => array(
						array(
							'id'        => 'plugin-updates',
							'label'     => __( 'Plugin Updates', 'stops-core-theme-and-plugin-updates' ),
							'items'     => $plugin_items,
							'context'   => 'plugins'
						),
						array(
							'id'        => 'theme-updates',
							'label'     => __( 'Theme Updates', 'stops-core-theme-and-plugin-updates' ),
							'items'     => $theme_items,
							'context'   => 'themes'
						)
					)
				)
			),
		);
		$boxes[] = array(
			'title' => __( 'Plugin and Theme Automatic Updates', 'stops-core-theme-and-plugin-updates' ),
			'items' => array( 
				array(
					'id'        => 'plugins-themes-automatic',
					'component' => 'ToggleTabs',
					'active'    => 'plugin-updates-automatic',
					'tabs' => array(
						array(
							'id'        => 'plugin-updates-automatic',
							'label'     => __( 'Plugin Updates', 'stops-core-theme-and-plugin-updates' ),
							'items'     => $plugin_automatic_items,
							'context'   => 'plugins_automatic'
						),
						array(
							'id'        => 'theme-updates-automatic',
							'label'     => __( 'Theme Updates', 'stops-core-theme-and-plugin-updates' ),
							'items'     => $theme_automatic_items,
							'context'   => 'themes_automatic'
						)
					)
				)
			)
		);
		$boxes[] = array(
			'title' => __( 'WordPress Notifications', 'stops-core-theme-and-plugin-updates' ),
			'component' => 'ToggleWrapper',
			'items' => array(
				array(
					'component' => 'ToggleItem',
					'title' => __( 'Core E-mails', 'stops-core-theme-and-plugin-updates' ),
					'name' => 'notification_core_update_emails',
					'disabled' => $this->get_json_maybe_disabled( 'notification_core_update_emails' ),
					'checked' => $this->get_json_maybe_checked( 'notification_core_update_emails' ),
					'loading' => false,
					'context' => 'core'
				),
			),
		);
		return $boxes;
	}
	
	public function enqueue_scripts() {
    	$pagenow = isset( $_GET[ 'page' ] ) ? $_GET[  'page' ] : false;
    	$is_active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : false;
    	
    	//Check to make sure we're on the mpsum admin page
    	if ( $pagenow != 'mpsum-update-options' ) {
            return;	
        }
        
        // Get user data
        $user_id = get_current_user_id();
		$dashboard_showing = get_user_meta( $user_id, 'mpsum_dashboard', true );
		
		// Get options
		$options = MPSUM_Updates_Manager::get_options( 'core' );
        
        wp_enqueue_script( 'sweetalert', MPSUM_Updates_Manager::get_plugin_url( '/js/source/sweetalert2.js' ), array( 'jquery' ), '6.6.6', true );
        //wp_enqueue_script( 'sweetalert2', MPSUM_Updates_Manager::get_plugin_url( '/js/source/sweetalert2.common.js' ), array( 'sweetalert', 'jquery' ), '6.6.6', true );
        
    	wp_enqueue_script( 'mpsum_dashboard', MPSUM_Updates_Manager::get_plugin_url( '/js/admin.js' ), array( 'jquery' ), '20170801', true );
    	
    	$user_id = get_current_user_id();
		$dashboard_showing = get_user_meta( $user_id, 'mpsum_dashboard', true );
		if ( ! $dashboard_showing ) {
			$dashboard_showing = 'on';
		}
		
		$options = $options = MPSUM_Updates_Manager::get_options();
		
		/**
		 * Filter whether a ratings nag is enabled/disabled or not
		 *
		 * @since 6.3.0
		 *
		 * @param bool true to show ratings nag, false if not
		 */
		$ratings_nag_showing = apply_filters( 'mpsum_ratings_nag', true );
		if ( isset( $options[ 'core' ][ 'ratings_nag' ] ) && false == $options[ 'core' ][ 'ratings_nag' ] ) {
			$ratings_nag_showing = false;
		}
		
		/**
		 * Filter whether a tracking nag is enabled/disabled or not
		 *
		 * @since 6.3.3
		 *
		 * @param bool true to show tracking nag, false if not
		 */
		$tracking_nag_showing = apply_filters( 'mpsum_tracking_nag', true );
		if ( isset( $options[ 'core' ][ 'tracking_nag' ] ) && 'off' == $options[ 'core' ][ 'tracking_nag' ] ) {
			$tracking_nag_showing = 'off';
		}
		
		$has_wizard = 'off';
		$maybe_has_wizard = MPSUM_Updates_Manager::get_options( 'core' );
		if ( empty( $maybe_has_wizard ) ) {
			$has_wizard = 'on';
		}
		
		//  tracking_nag
    	wp_localize_script( 'mpsum_dashboard', 'mpsum', array( 
    		'spinner'           => MPSUM_Updates_Manager::get_plugin_url( '/images/spinner.gif' ),
    		'tabs'              => _x( 'Tabs', 'Show or hide admin tabs', 'stops-core-theme-and-plugin-updates' ),
    		'dashboard'         => _x( 'Show Dashboard', 'Show or hide the dashboard', 'stops-core-theme-and-plugin-updates' ),
    		'dashboard_showing' => $dashboard_showing,
    		'enabled' => __( 'Enabled', 'stops-core-theme-and-plugin-updates' ),
    		'disabled' => __( 'Disabled', 'stops-core-theme-and-plugin-updates' ),
    		'admin_nonce' => wp_create_nonce( 'mpsum_options_save' ),
    		'ratings_nag' => array(
	    		'text' => __( 'Hey there! If Easy Updates Manager has helped you, can you do us a HUGE favor and give us a rating? THANKS! - The Easy Updates Manager team', 'stops-core-theme-and-plugin-updates' ),
	    		'url' => 'https://wordpress.org/support/plugin/stops-core-theme-and-plugin-updates/reviews/#new-post',
	    		'affirm' => __( 'Sure! Absolutely.', 'stops-core-theme-and-plugin-updates' ),
	    		'cancel' => __( 'No thanks!', 'stops-core-theme-and-plugin-updates' ),
	    		'enabled' => $ratings_nag_showing
    		),
    		'tracking_nag' => array(
	    		'text' => __( 'Please help us improve this plugin. We are working on a new admin interface for you, and we need your help. Once a month you can automatically send us helpful data on how you are using the plugin. You can always turn it off later in the Advanced section.', 'stops-core-theme-and-plugin-updates' ),
	    		'url' => 'https://easyupdatesmanager.com/tracking/',
	    		'affirm' => __( 'Sure! Absolutely!', 'stops-core-theme-and-plugin-updates' ),
	    		'cancel' => __( 'No Thanks, but Good Luck!', 'stops-core-theme-and-plugin-updates' ),
	    		'help' => __( 'Learn More.', 'stops-core-theme-and-plugin-updates' ),
	    		'enabled' => $tracking_nag_showing
	    	),
	    	'welcome' => __( 'Welcome to Easy Updates Manager.', 'stops-core-theme-and-plugin-updates' ),
	    	'welcome_intro' =>  __( 'What would you like to do?', 'stops-core-theme-and-plugin-updates' ),
	    	'welcome_automatic' =>  __( 'Turn on Automatic Updates', 'stops-core-theme-and-plugin-updates' ),
	    	'welcome_disable' =>  __( 'Disable All Updates (not recommended)', 'stops-core-theme-and-plugin-updates' ),
	    	'welcome_skip' =>  __( 'Configure Manually', 'stops-core-theme-and-plugin-updates' ),
	    	'new_user' => $has_wizard,
    	) );
    	wp_enqueue_style( 'mpsum_dashboard', MPSUM_Updates_Manager::get_plugin_url( '/css/style.css' ), array(), '20170801' );
    	wp_enqueue_style( 'sweetalert2', MPSUM_Updates_Manager::get_plugin_url( '/css/sweetalert2.css' ), array(), '20170801' );
    }
	
	/**
	* Adds a sub-menu page for multisite.
	*
	* Adds a sub-menu page for multisite.
	*
	* @since 5.0.0 
	* @access public
	* @see init
	* @internal Uses network_admin_menu action
	*
	*/
	public function init_network_admin_menus() {
		$hook = add_dashboard_page( __( 'Updates Options', 'stops-core-theme-and-plugin-updates' ) , __( 'Updates Options', 'stops-core-theme-and-plugin-updates' ), 'install_plugins', self::get_slug(), array( $this, 'output_admin_interface' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( "load-$hook", array( $this, 'init_help_screen' ) );
		add_action( "load-$hook", array( $this, 'init_screen_options' ) );
	}
	
	/**
	* Adds a sub-menu page for single-site.
	*
	* Adds a sub-menu page for single-site.
	*
	* @since 5.0.0 
	* @access public
	* @see init
	* @internal Uses admin_menu action
	*
	*/
	public function init_single_site_admin_menus() {
		$hook = add_dashboard_page( __( 'Updates Options', 'stops-core-theme-and-plugin-updates' ) , __( 'Updates Options', 'stops-core-theme-and-plugin-updates' ), 'install_plugins', self::get_slug(), array( $this, 'output_admin_interface' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( "load-$hook", array( $this, 'init_help_screen' ) );	
		add_action( "load-$hook", array( $this, 'init_screen_options' ) );
	}
	
	/**
	* Outputs admin interface for sub-menu.
	*
	* Outputs admin interface for sub-menu.
	*
	* @since 5.0.0 
	* @access public
	* @see init_network_admin_menus, init_single_site_admin_menus
	*
	*/
	public function output_admin_interface() {
		?>
		<div class="wrap">
			<h1>
				<?php echo esc_html_e( 'Manage Updates', 'stops-core-theme-and-plugin-updates' ); ?>
			</h1>
			<?php
            $core_options = MPSUM_Updates_Manager::get_options( 'core' );
			$tabs = array();
			
			if ( 'off' !== get_user_meta( get_current_user_id(), 'mpsum_dashboard', true ) ) {
				$tabs[] = array(
	    			'url'    => add_query_arg( array( 'tab' => 'dashboard' ), self::get_url() ), /* URL to the tab */
	    			'label'  => esc_html__( 'Dashboard', 'stops-core-theme-and-plugin-updates' ),
	    			'get'    => 'dashboard' /*$_GET variable*/,
	    			'action' => 'mpsum_admin_tab_dashboard' /* action variable in do_action */
	            );
			}
			
            $tabs[] = array(
				'url'    => add_query_arg( array( 'tab' => 'main' ), self::get_url() ), /* URL to the tab */
				'label'  => esc_html__( 'General', 'stops-core-theme-and-plugin-updates' ),
				'get'    => 'main' /*$_GET variable*/,
				'action' => 'mpsum_admin_tab_main' /* action variable in do_action */
			);
			$tabs[] = array(
				'url'    => add_query_arg( array( 'tab' => 'plugins' ), self::get_url() ), /* URL to the tab */
				'label'  => esc_html__( 'Plugins', 'stops-core-theme-and-plugin-updates' ),
				'get'    => 'plugins' /*$_GET variable*/,
				'action' => 'mpsum_admin_tab_plugins' /* action variable in do_action */
			);
			$tabs[] = array(
				'url'    => add_query_arg( array( 'tab' => 'themes' ), self::get_url() ), /* URL to the tab */
				'label'  => esc_html__( 'Themes', 'stops-core-theme-and-plugin-updates' ),
				'get'    => 'themes' /*$_GET variable*/,
				'action' => 'mpsum_admin_tab_themes' /* action variable in do_action */
			);
			if ( isset( $core_options[ 'logs' ] ) && 'on' == $core_options[ 'logs' ] ) {
        		$tabs[] = array(
					'url'    => add_query_arg( array( 'tab' => 'logs' ), self::get_url() ), /* URL to the tab */
					'label'  => esc_html__( 'Logs', 'stops-core-theme-and-plugin-updates' ),
					'get'    => 'logs' /*$_GET variable*/,
					'action' => 'mpsum_admin_tab_logs' /* action variable in do_action */
				);
    		}
    		$tabs[] = array(
				'url'    => add_query_arg( array( 'tab' => 'advanced' ), self::get_url() ), /* URL to the tab */
				'label'  => esc_html__( 'Advanced', 'stops-core-theme-and-plugin-updates' ),
				'get'    => 'advanced' /*$_GET variable*/,
				'action' => 'mpsum_admin_tab_advanced' /* action variable in do_action */
			);
			$tabs_count = count( $tabs );
			if ( $tabs && !empty( $tabs ) )  {
				$tab_html =  '<h2 class="nav-tab-wrapper">';
				$active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : 'dashboard';
				if ( 'off' === get_user_meta( get_current_user_id(), 'mpsum_dashboard', true ) && 'dashboard' == $active_tab ) {
					$active_tab = 'main';
				}
				$do_action = false;
				foreach( $tabs as $tab ) {
					$classes = array( 'nav-tab' );
					$tab_get = isset( $tab[ 'get' ] ) ? $tab[ 'get' ] : '';
					if ( $active_tab == $tab_get ) {
						$classes[] = 'nav-tab-active';
						$do_action = isset( $tab[ 'action' ] ) ? $tab[ 'action' ] : false;
					}
					$tab_url = isset( $tab[ 'url' ] ) ? $tab[ 'url' ] : '';
					$tab_label = isset( $tab[ 'label' ] ) ? $tab[ 'label' ] : ''; 
					$tab_html .= sprintf( '<a href="%s" class="%s">%s</a>', esc_url( $tab_url ), esc_attr( implode( ' ', $classes ) ), esc_html( $tab[ 'label' ] ) );
				}
				$tab_html .= '</h2>';
				if ( $tabs_count > 1 ) {
					echo $tab_html;	
				}
				if ( $do_action ) {
					
					/**
					* Perform a tab action.
					*
					* Perform a tab action.
					*
					* @since 5.0.0
					*
					* @param string $action Can be mpsum_admin_tab_main, mpsum_admin_tab_plugins, mpsum_admin_tab_themes, and mpsum_admin_tab_advanced.
					*/
					do_action( $do_action );	
				}
			}	
			?>
			
		</div><!-- .wrap -->
		<?php
	} //end output_admin_interface
	
	/**
	* Outputs admin interface for sub-menu.
	*
	* Outputs admin interface for sub-menu.
	*
	* @since 5.0.0 
	* @access public
	* @see __construct
	* @internal Uses $prefix . "plugin_action_links_$plugin_file" action
	* @return array Array of settings
	*/
	public function plugin_settings_link( $settings ) {
		$admin_anchor = sprintf( '<a href="%s">%s</a>', esc_url( $this->get_url() ), esc_html__( 'Configure', 'stops-core-theme-and-plugin-updates' ) );
		
		if ( ! is_array( $settings ) ) {
    		return array( $admin_anchor );
		} else {
    		return array_merge( array( $admin_anchor ), $settings );
		}
	}
}
