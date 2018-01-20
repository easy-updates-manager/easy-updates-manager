<?php
/**
 * Controls the themes tab
 *
 * Controls the themes tab and handles the saving of its options.
 *
 * @since 5.0.0
 *
 * @package WordPress
 */
class MPSUM_Admin_Themes {

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
	private $tab = 'themes';

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
		add_action( 'mpsum_admin_tab_themes', array( $this, 'tab_output_themes' ) );
		add_filter( 'mpsum_theme_action_links', array( $this, 'theme_action_links' ), 11, 2 );
		add_action( 'admin_init', array( $this, 'maybe_save_theme_options' ) );
	}

	/**
	* Determine whether the themes can be updated or not.
	*
	* Determine whether the themes can be updated or not.
	*
	* @since 5.0.0
	* @access private
	*
	* @return bool True if the themes can be updated, false if not.
	*/
	private function can_update() {
		$core_options = MPSUM_Updates_Manager::get_options( 'core' );
		if ( isset( $core_options[ 'all_updates' ] ) && 'off' == $core_options[ 'all_updates' ] ) {
			return false;
		}
		if ( isset( $core_options[ 'theme_updates' ] ) && 'off' == $core_options[ 'theme_updates' ] ) {
			return false;
		}
		return true;
	}

	/**
	* Determine whether the save the theme options or not.
	*
	* Determine whether the save the theme options or not.
	*
	* @since 5.0.0
	* @access public
	* @see __construct
	* @internal Uses admin_init action
	*
	*/
	public function maybe_save_theme_options() {
		if ( !current_user_can( 'update_themes' ) ) return;
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
		$theme_status = isset( $_REQUEST[ 'theme_status' ] ) ? $_REQUEST[ 'theme_status' ] : false;
		if ( false !== $theme_status ) {
			$query_args[ 'theme_status' ] = $theme_status;
		}

		//Save theme options
		$this->save_theme_update_options( $action );

		//Redirect back to settings screen
		wp_redirect( esc_url_raw( add_query_arg( $query_args, MPSUM_Admin::get_url() ) ) );
		exit;
	}

	/**
	* Save the theme options based on the passed action.
	*
	* Save the theme options based on the passed action.
	*
	* @since 5.0.0
	* @access private
	* @see maybe_save_theme_options
	* @param string $action Action to take action on
	*
	*/
	private function save_theme_update_options( $action ) {
		//Check capability
		$capability = 'update_themes'; //On single site, admins can use this, on multisite, only network admins can
		if ( !current_user_can( $capability ) ) return;

		$themes = isset( $_REQUEST[ 'checked' ] ) ? (array) $_REQUEST[ 'checked' ] : array();
		$theme_options = MPSUM_Updates_Manager::get_options( 'themes' );
		$theme_automatic_options = MPSUM_Updates_Manager::get_options( 'themes_automatic' );

		switch( $action ) {
			case 'disallow-update-selected':
				foreach( $themes as $theme ) {
					$theme_options[] = $theme;
					if ( ( $key = array_search( $theme, $theme_automatic_options ) ) !== false ) {
						unset( $theme_automatic_options[ $key ] );
					}
				}

				break;
			case 'allow-update-selected':
				foreach( $themes as $theme ) {
					if ( ( $key = array_search( $theme, $theme_options ) ) !== false ) {
						unset( $theme_options[ $key ] );
					}
				}
				break;
			case 'allow-automatic-selected':
				foreach( $themes as $theme ) {
					$theme_automatic_options[] = $theme;
					if ( ( $key = array_search( $theme, $theme_options ) ) !== false ) {
						unset( $theme_options[ $key ] );
					}
				}
				break;
			case 'disallow-automatic-selected':
				foreach( $themes as $theme ) {
					if ( ( $key = array_search( $theme, $theme_automatic_options ) ) !== false ) {
						unset( $theme_automatic_options[ $key ] );
					}
				}
				break;
			default:
				return;
		}
		//Check nonce
		check_admin_referer( 'mpsum_theme_update', '_mpsum' );

		//Update option
		$theme_options = array_values( array_unique( $theme_options ) );
		$theme_automatic_options = array_values( array_unique( $theme_automatic_options ) );
		$options = MPSUM_Updates_Manager::get_options();
		$options[ 'themes' ] = $theme_options;
		$options[ 'themes_automatic' ] = $theme_automatic_options;
		MPSUM_Updates_Manager::update_options( $options );
	}

	/**
	* Output the HTML interface for the themes tab.
	*
	* Output the HTML interface for the themes tab.
	*
	* @since 5.0.0
	* @access public
	* @see __construct
	* @internal Uses the mpsum_admin_tab_themes action
	*/
	public function tab_output_themes() {
		if ( isset( $_GET[ 'action' ] ) ) {
			$message = __( 'Settings have been updated.', 'stops-core-theme-and-plugin-updates' );
			if ( 'allow-automatic-selected' == $_GET[ 'action' ] ) {
				$message = __( 'The selected themes have had automatic updates enabled.', 'stops-core-theme-and-plugin-updates' );
			} elseif( 'disallow-automatic-selected' == $_GET[ 'action' ] ) {
				$message = __( 'The selected themes have had automatic updates disabled.', 'stops-core-theme-and-plugin-updates' );
			} elseif( 'disallow-update-selected' == $_GET[ 'action' ] ) {
					$message = __( 'The selected theme updates have been disabled.', 'stops-core-theme-and-plugin-updates' );
			} elseif( 'allow-update-selected' == $_GET[ 'action' ] ) {
				$message = __( 'The selected theme updates have been enabled.', 'stops-core-theme-and-plugin-updates' );
			}
			?>
			<div class="updated"><p><strong><?php echo esc_html( $message ); ?></strong></p></div>
			<?php
		}


		?>
        <form action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="post">
	    <?php
		$theme_status = isset( $_GET[ 'theme_status' ] ) ? $_GET[ 'theme_status' ] : false;
		if ( false !== $theme_status ) {
			printf( '<input type="hidden" name="theme_status" value="%s" />', esc_attr( $theme_status ) );
		}
		wp_nonce_field( 'mpsum_theme_update', '_mpsum' );
		?>
        <h3><?php esc_html_e( 'Theme Update Options', 'stops-core-theme-and-plugin-updates' ); ?></h3>
        <?php
		$core_options = MPSUM_Updates_Manager::get_options( 'core' );

		if ( false === $this->can_update() ) {
			printf( '<div class="error"><p><strong>%s</strong></p></div>', esc_html__( 'All theme updates have been disabled.', 'stops-core-theme-and-plugin-updates' ) );
		}
		$theme_table = new MPSUM_Themes_List_Table( $args = array( 'screen' => $this->slug, 'tab' => $this->tab ) );
		$theme_table->prepare_items();
		$theme_table->views();
		$theme_table->display();
		?>
        </form>
    <?php
	} //end tab_output_plugins

	/**
	* Outputs the theme action links beneath each theme row.
	*
	* Outputs the theme action links beneath each theme row.
	*
	* @since 5.0.0
	* @access public
	* @see __construct
	* @internal uses mpsum_theme_action_links filter
	*
	* @param array  $settings Array of settings to output.
	* @param WP_Theme $theme The theme object to take action on.
	*/
	public function theme_action_links( $settings, $theme ) {
		return $settings;
	}
}
