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
	
	public function enqueue_scripts() {
    	$pagenow = isset( $_GET[ 'page' ] ) ? $_GET[  'page' ] : false;
    	$is_active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : false;
    	
    	//Check to make sure we're on the mpsum admin page
    	if ( $pagenow != 'mpsum-update-options' ) {
            return;	
        }
        
    	wp_enqueue_script( 'mpsum_dashboard', MPSUM_Updates_Manager::get_plugin_url( '/js/admin.js' ), array( 'jquery' ), '20160817', true );
    	
    	$user_id = get_current_user_id();
		$dashboard_showing = get_user_meta( $user_id, 'mpsum_dashboard', true );
		if ( ! $dashboard_showing ) {
			$dashboard_showing = 'on';
		}
    	wp_localize_script( 'mpsum_dashboard', 'mpsum', array( 
    		'spinner'           => MPSUM_Updates_Manager::get_plugin_url( '/images/spinner.gif' ),
    		'tabs'              => _x( 'Tabs', 'Show or hide admin tabs', 'stops-core-theme-and-plugin-updates' ),
    		'dashboard'         => _x( 'Show Dashboard', 'Show or hide the dashboard', 'stops-core-theme-and-plugin-updates' ),
    		'dashboard_showing' => $dashboard_showing,
    	) );
    	wp_enqueue_style( 'mpsum_dashboard', MPSUM_Updates_Manager::get_plugin_url( '/css/style.css' ), array(), '20160819' );
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
