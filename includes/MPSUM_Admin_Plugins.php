<?php
/**
 * Controls the plugins tab
 *
 * Controls the plugins tab and handles the saving of its options.
 *
 * @since 5.0.0
 *
 * @package WordPress
 */
class MPSUM_Admin_Plugins {
	/**
	* Holds the slug to the admin panel page
	*
	* @since 5.0.0
	* @access private
	* @var string $slug
	*/
	private $slug = '';

	/**
	* Holds the tab name
	*
	* @since 5.0.0
	* @access static
	* @var string $tab
	*/
	private $tab = 'plugins';

	/**
	* Class constructor.
	*
	* Initialize the class
	*
	* @since 5.0.0
	* @access public
	*
	* @param string $slug Slug to the admin panel page
	*/
	public function __construct( $slug = '' ) {
		$this->slug = $slug;
		//Admin Tab Actions
		add_action( 'mpsum_admin_tab_plugins', array( $this, 'tab_output_plugins' ) );
		add_filter( 'mpsum_plugin_action_links', array( $this, 'plugin_action_links' ), 11, 2 );
		add_action( 'admin_init', array( $this, 'maybe_save_plugin_options' ) );
	}

	/**
	* Determine whether the plugins can be updated or not.
	*
	* Determine whether the plugins can be updated or not.
	*
	* @since 5.0.0
	* @access private
	*
	* @return bool True if the plugins can be updated, false if not.
	*/
	private function can_update() {
		$core_options = MPSUM_Updates_Manager::get_options( 'core' );
		if ( isset( $core_options[ 'all_updates' ] ) && 'off' == $core_options[ 'all_updates' ] ) {
			return false;
		}
		if ( isset( $core_options[ 'plugin_updates' ] ) && 'off' == $core_options[ 'plugin_updates' ] ) {
			return false;
		}
		return true;
	}

	/**
	* Determine whether the save the plugin options or not.
	*
	* Determine whether the save the plugin options or not.
	*
	* @since 5.0.0
	* @access public
	* @see __construct
	* @internal Uses admin_init action
	*
	*/
	public function maybe_save_plugin_options() {
		if ( !current_user_can( 'update_plugins' ) ) return;
		if ( !isset( $_GET[ 'page' ] ) || $_GET[ 'page' ] != $this->slug ) return;
		if ( !isset( $_GET[ 'tab' ] ) || $_GET[ 'tab' ] != $this->tab ) return;
		if ( !isset( $_REQUEST[ 'action' ] ) && ! isset( $_REQUEST[ 'action2' ] ) ) return;
		if ( !isset( $_REQUEST[ '_mpsum' ] ) ) return;

		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST[ 'action' ] )
			$action = $_REQUEST[ 'action' ];

		if ( isset( $_REQUEST[ 'action2' ] ) && -1 != $_REQUEST[ 'action2' ] )
			$action = $_REQUEST[ 'action2' ];


		//Build Query Args
		$paged = isset( $_GET[ 'paged' ] ) ? absint( $_GET[ 'paged' ] ) : false;
		$query_args = array();
		$query_args[ 'page' ] = $this->slug;
		if ( false !== $paged ) {
			$query_args[ 'paged' ] = $paged;
		}
		$query_args[ 'action' ] = $action;
		$query_args[ 'tab' ] = $this->tab;
		$plugin_status = isset( $_REQUEST[ 'plugin_status' ] ) ? $_REQUEST[ 'plugin_status' ] : false;
		if ( false !== $plugin_status ) {
			$query_args[ 'plugin_status' ] = $plugin_status;
		}

		//Save theme options
		$this->save_plugin_update_options( $action );

		//Redirect back to settings screen
		wp_redirect( esc_url_raw( add_query_arg( $query_args, MPSUM_Admin::get_url() ) ) );
		exit;
	}

	/**
	* Outputs the plugin action links beneath each plugin row.
	*
	* Outputs the plugin action links beneath each plugin row.
	*
	* @since 5.0.0
	* @access public
	* @see __construct
	* @internal uses mpsum_plugin_action_links filter
	*
	* @param array  $settings Array of settings to output.
	* @param string $plugin The relative plugin path.
	*/
	public function plugin_action_links( $settings, $plugin ) {
		return $settings;
	}

	/**
	* Save the plugin options based on the passed action.
	*
	* Save the plugin options based on the passed action.
	*
	* @since 5.0.0
	* @access private
	* @see maybe_save_plugin_options
	* @param string $action Action to take action on
	*
	*/
	private function save_plugin_update_options( $action ) {
		//Check capability
		$capability = 'update_plugins'; //On single site, admins can use this, on multisite, only network admins can
		if ( !current_user_can( $capability ) ) return;

		$plugins = isset( $_REQUEST[ 'checked' ] ) ? (array) $_REQUEST[ 'checked' ] : array();
		$plugin_options = MPSUM_Updates_Manager::get_options( 'plugins' );
		$plugin_automatic_options = MPSUM_Updates_Manager::get_options( 'plugins_automatic' );
		switch( $action ) {
			case 'disallow-update-selected':
				foreach( $plugins as $plugin ) {
					$plugin_options[] = $plugin;
					if ( ( $key = array_search( $plugin, $plugin_automatic_options ) ) !== false ) {
						unset( $plugin_automatic_options[ $key ] );
					}
				}

				break;
			case 'allow-update-selected':
				foreach( $plugins as $plugin ) {
					if ( ( $key = array_search( $plugin, $plugin_options ) ) !== false ) {
						unset( $plugin_options[ $key ] );
					}
				}
				break;
			case 'allow-automatic-selected':
				foreach( $plugins as $plugin ) {
					$plugin_automatic_options[] = $plugin;
					if ( ( $key = array_search( $plugin, $plugin_options ) ) !== false ) {
						unset( $plugin_options[ $key ] );
					}
				}
				break;
			case 'disallow-automatic-selected':
				foreach( $plugins as $plugin ) {
					if ( ( $key = array_search( $plugin, $plugin_automatic_options ) ) !== false ) {
						unset( $plugin_automatic_options[ $key ] );
					}
				}
				break;
			default:
				return;
		}
		//Check nonce

		check_admin_referer( 'mpsum_plugin_update', '_mpsum' );

		//Update option
		$plugin_options = array_values( array_unique( $plugin_options ) );
		$plugin_automatic_options = array_values( array_unique( $plugin_automatic_options ) );
		$options = MPSUM_Updates_Manager::get_options();
		$options[ 'plugins' ] = $plugin_options;
		$options[ 'plugins_automatic' ] = $plugin_automatic_options;
		MPSUM_Updates_Manager::update_options( $options );
	}

	/**
	* Output the HTML interface for the plugins tab.
	*
	* Output the HTML interface for the plugins tab.
	*
	* @since 5.0.0
	* @access public
	* @see __construct
	* @internal Uses the mpsum_admin_tab_plugins action
	*/
	public function tab_output_plugins() {
		if ( isset( $_GET[ 'action' ] ) ) {
			$message = __( 'Settings have been updated.', 'stops-core-theme-and-plugin-updates' );

			if ( 'allow-automatic-selected' == $_GET[ 'action' ] ) {
				$message = __( 'The selected plugins have had automatic updates enabled.', 'stops-core-theme-and-plugin-updates' );
			} elseif( 'disallow-automatic-selected' == $_GET[ 'action' ] ) {
				$message = __( 'The selected plugins have had automatic updates disabled.', 'stops-core-theme-and-plugin-updates' );
			} elseif( 'disallow-update-selected' == $_GET[ 'action' ] ) {
					$message = __( 'The selected plugin updates have been disabled.', 'stops-core-theme-and-plugin-updates' );
			} elseif( 'allow-update-selected' == $_GET[ 'action' ] ) {
				$message = __( 'The selected plugin updates have been enabled.', 'stops-core-theme-and-plugin-updates' );
			}
			?>
			<div class="updated"><p><strong><?php echo esc_html( $message ); ?></strong></p></div>
			<?php
		}

		?>
        <form action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="post">
	    <?php
		$plugin_status = isset( $_GET[ 'plugin_status' ] ) ? $_GET[ 'plugin_status' ] : false;
		if ( false !== $plugin_status ) {
			printf( '<input type="hidden" name="plugin_status" value="%s" />', esc_attr( $plugin_status ) );
		}
		wp_nonce_field( 'mpsum_plugin_update', '_mpsum' );
		?>
        <h3><?php esc_html_e( 'Plugin Update Options', 'stops-core-theme-and-plugin-updates' ); ?></h3>
        <?php
	    $core_options = MPSUM_Updates_Manager::get_options( 'core' );
	    if ( false === $this->can_update() ) {
			printf( '<div class="error"><p><strong>%s</strong></p></div>', esc_html__( 'All plugin updates have been disabled.', 'stops-core-theme-and-plugin-updates' ) );
		}
		$plugin_table = new MPSUM_Plugins_List_Table( $args = array( 'screen' => $this->slug, 'tab' => $this->tab ) );
		$plugin_table->prepare_items();
		$plugin_table->views();
		$plugin_table->display();
		?>
        </form>
    <?php
	} //end tab_output_plugins
}
