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
		add_filter( 'admin_footer_text', array( $this, 'ratings_nag' ) );
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

		wp_enqueue_script( 'mpsum_dashboard', MPSUM_Updates_Manager::get_plugin_url( '/js/admin.js' ), array( 'jquery' ), '20180126', true );

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

		$I18N = array(
			'default'                                    => _x( 'Default', 'Option as Default', 'stops-core-theme-and-plugin-updates' ),
			'on'                                         => _x( 'On', 'Option enabled', 'stops-core-theme-and-plugin-updates' ),
			'off'                                        => _x( 'Off', 'Option disabled', 'stops-core-theme-and-plugin-updates' ),
			'custom'                                     => _x( 'Custom', 'Option allows for configuration', 'stops-core-theme-and-plugin-updates' ),
			'automatic_updates_default_status'           => __( 'You have selected default. WordPress will behave as if this plugin is not installed for automatic updates.', 'stops-core-theme-and-plugin-updates' ),
			'automatic_updates_on_status'                => __( 'Automatic updates are on.', 'stops-core-theme-and-plugin-updates' ),
			'automatic_updates_off_status'               => __( 'Automatic updates are off.', 'stops-core-theme-and-plugin-updates', 'stops-core-theme-and-plugin-updates' ),
			'automatic_updates_custom_status'            => __( 'You have selected to customize the updates below.', 'stops-core-theme-and-plugin-updates' ),
			'automatic_updates'                          => __( 'Automatic Updates', 'stops-core-theme-and-plugin-updates' ),
			'automatic_updates_description'              => __( 'These options will enable or disable automatic updates (background updates) of certain parts of WordPress. Select Custom for more flexibility. Leave as Default for normal WordPress behavior', 'stops-core-theme-and-plugin-updates' ),
			'major_releases'                             => _x( 'Major Releases', 'Major WordPress releases', 'stops-core-theme-and-plugin-updates' ),
			'major_releases_description'                 => __( 'Automatically update to major releases (e.g., 4.1, 4.2, 4.3).', 'stops-core-theme-and-plugin-updates' ),
			'major_releases_label_on'                    => __( 'Enable Major Releases', 'stops-core-theme-and-plugin-updates' ),
			'major_releases_label_on_status'             => __( 'Automatic major release updates are now turned on.', 'stops-core-theme-and-plugin-updates' ),
			'major_releases_label_off'                   => __( 'Disable Major Releases', 'stops-core-theme-and-plugin-updates' ),
			'major_releases_label_off_status'            => __( 'Automatic major release updates are now turned off.', 'stops-core-theme-and-plugin-updates' ),
			'minor_releases'                             => _x( 'Minor Releases', 'Minor point releases for WordPress', 'stops-core-theme-and-plugin-updates' ),
			'minor_releases_description'                 => __( 'Automatically update to minor releases (e.g., 4.1.1, 4.1.2, 4.1.3).', 'stops-core-theme-and-plugin-updates' ),
			'minor_releases_label_on'                    => __( 'Enable Minor Releases', 'stops-core-theme-and-plugin-updates' ),
			'minor_releases_label_on_status'             => __( 'Automatic minor release updates are now turned on.', 'stops-core-theme-and-plugin-updates' ),
			'minor_releases_label_off'                   => __( 'Disable Minor Releases', 'stops-core-theme-and-plugin-updates' ),
			'minor_releases_label_off_status'            => __( 'Automatic minor release updates are now turned off.', 'stops-core-theme-and-plugin-updates' ),
			'development_releases'                       => _x( 'Development Updates', 'Beta and RC releases for WordPress', 'stops-core-theme-and-plugin-updates' ),
			'development_releases_description'           => __( 'Allow your install to receive development updates from WordPress (for advanced users only)', 'stops-core-theme-and-plugin-updates' ),
			'development_releases_label_on'              => __( 'Enable Development Updates', 'stops-core-theme-and-plugin-updates' ),
			'development_releases_label_on_status'       => __( 'Automatic development release updates are now turned on.', 'stops-core-theme-and-plugin-updates' ),
			'development_releases_label_off'             => __( 'Disable Development Updates', 'stops-core-theme-and-plugin-updates' ),
			'development_releases_label_off_status'      => __( 'Automatic development release updates are now turned off.', 'stops-core-theme-and-plugin-updates' ),
			'translation_releases'                       => _x( 'Translation Updates', 'Enable or disable translation updates', 'stops-core-theme-and-plugin-updates' ),
			'translation_releases_description'           => __( 'Automatically update your translations.', 'stops-core-theme-and-plugin-updates' ),
			'translation_releases_label_on'              => __( 'Enable Translation Updates', 'stops-core-theme-and-plugin-updates' ),
			'translation_releases_label_on_status'       => __( 'Automatic translation updates are now turned on.', 'stops-core-theme-and-plugin-updates' ),
			'translation_releases_label_off'             => __( 'Disable Translation Updates', 'stops-core-theme-and-plugin-updates' ),
			'translation_releases_label_off_status'      => __( 'Automatic translation updates are now turned off.', 'stops-core-theme-and-plugin-updates' ),
			'select_individually'                        => __( 'Select Individually', 'stops-core-theme-and-plugin-updates' ),
			'automatic_plugin_updates'                   => __( 'Automatic Plugin Updates', 'stops-core-theme-and-plugin-updates' ),
			'automatic_plugin_updates_description'       => __( 'Automatically update your plugins. Select always on, always off, the WordPress default, or select plugins individually using the Plugins tab.', 'stops-core-theme-and-plugin-updates' ),
			'automatic_plugin_updates_default_status'    => __( 'Automatic updates for plugins are now at their default setting (default is off).', 'stops-core-theme-and-plugin-updates' ),
			'automatic_plugin_updates_on_status'         => __( 'Automatic updates for plugins are now on.', 'stops-core-theme-and-plugin-updates' ),
			'automatic_plugin_updates_off_status'        => __( 'Automatic updates for plugins are now off.', 'stops-core-theme-and-plugin-updates' ),
			'automatic_plugin_updates_individual_status' => __( 'Automatic updates for plugins can be customized in the Plugins tab.', 'stops-core-theme-and-plugin-updates' ),
			'automatic_theme_updates'                    => __( 'Automatic Theme Updates', 'stops-core-theme-and-plugin-updates' ),
			'automatic_theme_updates_description'        => __( 'Automatically update your themes. Select always on, always off, the WordPress default, or select themes individually using the Themes tab.', 'stops-core-theme-and-plugin-updates' ),
			'automatic_theme_updates_default_status'     => __( 'Automatic updates for themes are now at their default setting (default is off).', 'stops-core-theme-and-plugin-updates' ),
			'automatic_theme_updates_on_status'          => __( 'Automatic updates for themes are now on.', 'stops-core-theme-and-plugin-updates' ),
			'automatic_theme_updates_off_status'         => __( 'Automatic updates for themes are now off.', 'stops-core-theme-and-plugin-updates' ),
			'automatic_theme_updates_individual_status'  => __( 'Automatic updates for themes can be customized in the Themes tab.', 'stops-core-theme-and-plugin-updates' ),
			'disable_updates'                            => __( 'Disable All Updates', 'stops-core-theme-and-plugin-updates' ),
			'disable_updates_description'                => __( 'This is a master switch and will enable or disable updates for the WordPress installation. Switching updates off is not recommended.', 'stops-core-theme-and-plugin-updates' ),
			'disable_updates_label_on'                   => __( 'Enable All Updates ', 'stops-core-theme-and-plugin-updates' ),
			'disable_updates_label_on_status'            => __( 'All updates are allowed, however, you still need to configure the updates below.', 'stops-core-theme-and-plugin-updates' ),
			'disable_updates_label_off'                  => __( 'Disable All Updates ', 'stops-core-theme-and-plugin-updates' ),
			'disable_updates_label_off_status'           => __( 'All updates are disabled.', 'stops-core-theme-and-plugin-updates' ),
			'logs'                                       => _x( 'Logs', 'Log what is stored when assets update', 'stops-core-theme-and-plugin-updates' ),
			'logs_description'                           => __( 'Logs will show you what assets have updated and will show up in the Logs tab.', 'stops-core-theme-and-plugin-updates' ),
			'logs_label_on'                              => __( 'Enable Logs', 'stops-core-theme-and-plugin-updates' ),
			'logs_label_on_status'                       => __( 'Logs are enabled. You will find Logs in the Logs tab.', 'stops-core-theme-and-plugin-updates' ),
			'logs_label_off'                             => __( 'Disable Logs', 'stops-core-theme-and-plugin-updates' ),
			'logs_label_off_status'                      => __( 'Logs are disabled.', 'stops-core-theme-and-plugin-updates' ),
			'browser_nag'                                => _x( 'Browser Nag', 'WordPress shows a warning for older browsers', 'stops-core-theme-and-plugin-updates' ),
			'browser_nag_description'                    => __( 'Enables or disables the browser nag for users using older browsers.', 'stops-core-theme-and-plugin-updates' ),
			'browser_nag_label_on'                       => __( 'Enable the Browser Nag', 'stops-core-theme-and-plugin-updates' ),
			'browser_nag_label_on_status'                => __( 'The Browser Nag for older browsers is on.', 'stops-core-theme-and-plugin-updates' ),
			'browser_nag_label_off'                      => __( 'Disable the Browser Nag', 'stops-core-theme-and-plugin-updates' ),
			'browser_nag_label_off_status'               => __( 'The Browser Nag for older browsers is off.', 'stops-core-theme-and-plugin-updates' ),
			'version_footer'                             => __( 'WordPress Version in Footer', 'stops-core-theme-and-plugin-updates' ),
			'version_footer_description'                 => __( 'Enables or disables the WordPress version from showing in the footer of the admin area.', 'stops-core-theme-and-plugin-updates' ),
			'version_footer_label_on'                    => __( 'Enable the Version in the Footer', 'stops-core-theme-and-plugin-updates' ),
			'version_footer_label_on_status'             => __( 'Showing the WordPress version in the footer is on.', 'stops-core-theme-and-plugin-updates' ),
			'version_footer_label_off_status'            => __( 'Showing the WordPress version in the footer is off.', 'stops-core-theme-and-plugin-updates' ),
			'version_footer_label_off'                   => __( 'Disable the Version in the Footer', 'stops-core-theme-and-plugin-updates' ),
			'emails'                                     => __( 'Core Notification E-mails', 'stops-core-theme-and-plugin-updates' ),
			'emails_description'                         => __( 'WordPress periodically sends update notification e-mails, such as in the case of automatic updates. By default, the email used is the one in Settings->General, but you can override this below.', 'stops-core-theme-and-plugin-updates' ),
			'emails_label_on'                            => __( 'Enable Core Notification E-mails', 'stops-core-theme-and-plugin-updates' ),
			'emails_label_on_status'                     => __( 'E-mail notifications are on. You can configure which e-mail addresses are sent to below.', 'stops-core-theme-and-plugin-updates' ),
			'emails_label_off'                           => __( 'Disable Core Notification E-mails', 'stops-core-theme-and-plugin-updates' ),
			'emails_label_off_status'                    => __( 'E-mail notifications are off', 'stops-core-theme-and-plugin-updates' ),
			'emails_placeholder'                         => __( 'Add an e-mail address', 'stops-core-theme-and-plugin-updates' ),
			'emails_input_label'                         => __( 'Enter Comma Separated E-mail Addresses', 'stops-core-theme-and-plugin-updates' ),
			'emails_invalid'                             => __( 'One or more e-mail addresses are invalid.', 'stops-core-theme-and-plugin-updates' ),
			'emails_save'                                => __( 'Save E-mail Addresses', 'stops-core-theme-and-plugin-updates' ),
			'emails_save_empty'                          => __( 'Please enter an e-mail address', 'stops-core-theme-and-plugin-updates' ),
			'emails_saving'                              =>__( 'Saving...', 'stops-core-theme-and-plugin-updates' ),
			'core_updates'                               => __( 'WordPress Core Updates', 'stops-core-theme-and-plugin-updates' ),
			'core_updates_description'                   => __( 'This allows you to disable or enable all core updates, including automatic updates.', 'stops-core-theme-and-plugin-updates' ),
			'core_updates_label_on'                      => __( 'Enable Core Updates ', 'stops-core-theme-and-plugin-updates' ),
			'core_updates_label_on_status'               => __( 'Core updates are enabled.', 'stops-core-theme-and-plugin-updates' ),
			'core_updates_label_off'                     => __( 'Disable Core Updates ', 'stops-core-theme-and-plugin-updates' ),
			'core_updates_label_off_status'              => __( 'Core updates are disabled.', 'stops-core-theme-and-plugin-updates' ),
			'plugin_updates'                             => __( 'WordPress Plugin Updates', 'stops-core-theme-and-plugin-updates' ),
			'plugin_updates_description'                 => __( 'This allows you to disable or enable all plugin updates. Disabling this option will also disable automatic updates.', 'stops-core-theme-and-plugin-updates' ),
			'plugin_updates_label_on'                    => __( 'Enable Plugin Updates ', 'stops-core-theme-and-plugin-updates' ),
			'plugin_updates_label_on_status'             => __( 'Plugin updates are enabled.', 'stops-core-theme-and-plugin-updates' ),
			'plugin_updates_label_off'                   => __( 'Disable Plugin Updates ', 'stops-core-theme-and-plugin-updates' ),
			'plugin_updates_label_off_status'            => __( 'Plugin updates are disabled.', 'stops-core-theme-and-plugin-updates' ),
			'theme_updates'                              => __( 'WordPress Theme Updates', 'stops-core-theme-and-plugin-updates' ),
			'theme_updates_description'                  => __( 'This allows you to disable or enable all theme updates. Disabling this option will also disable automatic updates.', 'stops-core-theme-and-plugin-updates' ),
			'theme_updates_label_on'                     => __( 'Enable Theme Updates ', 'stops-core-theme-and-plugin-updates' ),
			'theme_updates_label_on_status'              => __( 'Theme updates are enabled.', 'stops-core-theme-and-plugin-updates' ),
			'theme_updates_label_off'                    => __( 'Disable Theme Updates ', 'stops-core-theme-and-plugin-updates' ),
			'theme_updates_label_off_status'             => __( 'Theme updates are disabled.', 'stops-core-theme-and-plugin-updates' ),
			'translation_updates'                        => __( 'WordPress Translation Updates', 'stops-core-theme-and-plugin-updates' ),
			'translation_updates_description'            => __( 'This allows you to disable or enable all translations. Disabling this option will also disable automatic translation updates.', 'stops-core-theme-and-plugin-updates' ),
			'translation_updates_label_on'               => __( 'Enable Translation Updates ', 'stops-core-theme-and-plugin-updates' ),
			'translation_updates_label_on_status'        => __( 'Translation updates are enabled.', 'stops-core-theme-and-plugin-updates' ),
			'translation_updates_label_off'              => __( 'Disable Translation Updates ', 'stops-core-theme-and-plugin-updates' ),
			'translation_updates_label_off_status'       => __( 'Translation updates are disabled.', 'stops-core-theme-and-plugin-updates' ),
		);

		//  tracking_nag
		wp_localize_script( 'mpsum_dashboard', 'mpsum', array(
			'spinner'           => MPSUM_Updates_Manager::get_plugin_url( '/images/spinner.gif' ),
			'tabs'              => _x( 'Tabs', 'Show or hide admin tabs', 'stops-core-theme-and-plugin-updates' ),
			'dashboard'         => _x( 'Show Dashboard', 'Show or hide the dashboard', 'stops-core-theme-and-plugin-updates' ),
			'dashboard_showing' => $dashboard_showing,
			'enabled'           => __( 'Enabled', 'stops-core-theme-and-plugin-updates' ),
			'disabled'          => __( 'Disabled', 'stops-core-theme-and-plugin-updates' ),
			'admin_nonce'       => wp_create_nonce( 'mpsum_options_save' ),
			'ratings_nag'       => array(
				'text'          => __( 'Hey there! If Easy Updates Manager has helped you, can you do us a HUGE favor and give us a rating? THANKS! - The Easy Updates Manager team', 'stops-core-theme-and-plugin-updates' ),
				'url'           => 'https://wordpress.org/support/plugin/stops-core-theme-and-plugin-updates/reviews/#new-post',
				'affirm'        => __( 'Sure! Absolutely.', 'stops-core-theme-and-plugin-updates' ),
				'cancel'        => __( 'No thanks!', 'stops-core-theme-and-plugin-updates' ),
				'enabled'       => $ratings_nag_showing
			),
			'tracking_nag'      => array(
				'text'          => __( 'Please help us improve this plugin. We are working on a new admin interface for you, and we need your help. Once a month you can automatically send us helpful data on how you are using the plugin. You can always turn it off later in the Advanced section.', 'stops-core-theme-and-plugin-updates' ),
				'url'           => 'https://easyupdatesmanager.com/tracking/',
				'affirm'        => __( 'Sure! Absolutely!', 'stops-core-theme-and-plugin-updates' ),
				'cancel'        => __( 'No Thanks, but Good Luck!', 'stops-core-theme-and-plugin-updates' ),
				'help'          => __( 'Learn More.', 'stops-core-theme-and-plugin-updates' ),
				'enabled'       => $tracking_nag_showing
			),
			'rest_nonce'        => wp_create_nonce( 'wp_rest' ),
			'rest_url'          => get_rest_url( null, '/eum/v1/' ),
			'I18N'              => $I18N,
		) );
		wp_enqueue_style( 'mpsum_dashboard', MPSUM_Updates_Manager::get_plugin_url( '/css/style.css' ), array(), '20180126' );
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

			$tabs[] = array(
				'url'    => add_query_arg( array( 'tab' => 'general' ), self::get_url() ), /* URL to the tab */
				'label'  => esc_html__( 'General', 'stops-core-theme-and-plugin-updates' ),
				'get'    => 'general' /*$_GET variable*/,
				'action' => 'mpsum_admin_tab_dashboard' /* action variable in do_action */
			);

			/*$tabs[] = array(
				'url'    => add_query_arg( array( 'tab' => 'main' ), self::get_url() ),
				'label'  => esc_html__( 'General', 'stops-core-theme-and-plugin-updates' ),
				'get'    => 'main' ,
				'action' => 'mpsum_admin_tab_main'
			);
			*/

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
				$active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : 'general';
				if ( 'general' == $active_tab ) {
					$active_tab = 'general';
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

	/**
	* Add a ratings nag to the footer.
	*
	* Add a ratings nag to the footer.
	*
	* @since 7.0.0
	* @access static
	*
	* @return string URL to the admin panel page.
	*/
	public function ratings_nag( $text ) {

		if ( ! isset( $_GET[ 'page' ] ) || 'mpsum-update-options' != $_GET[ 'page' ] ) {
			return $text;
		}


		$text = sprintf( __( 'Thank you for creating with <a href="%s">WordPress</a>.' ), __( 'https://wordpress.org/' ) );

		$return = '<span id="footer-thankyou">';
		$return .= $text;
		$return .= sprintf( ' <a href="%s">%s <img src="%s" alt="Five Star Rating" /> ', esc_url( 'https://wordpress.org/support/plugin/stops-core-theme-and-plugin-updates/reviews/#new-post' ), esc_html__( 'Please Rate Easy Updates Manager!', 'stops-core-theme-and-plugin-updates' ), esc_url( MPSUM_Updates_Manager::get_plugin_url( '/images/ratings.png' ) ) );
		$return .= '</span>';
		return $return;

	}
}
