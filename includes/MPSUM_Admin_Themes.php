<?php
class MPSUM_Admin_Themes {
	private $slug = '';
	private $tab = 'themes';
	public function __construct( $slug = '' ) {
		$this->slug = $slug;
		//Admin Tab Actions
		add_action( 'mpsum_admin_tab_themes', array( $this, 'tab_output_themes' ) );	
		add_filter( 'mpsum_theme_action_links', array( $this, 'theme_action_links' ), 11, 2 );
		add_action( 'admin_init', array( $this, 'maybe_save_theme_options' ) );
	}
		
	public function maybe_save_theme_options() {
		if ( !current_user_can( 'update_themes' ) ) return;
		if ( !isset( $_GET[ 'page' ] ) || $_GET[ 'page' ] != $this->slug ) return;
		if ( !isset( $_GET[ 'tab' ] ) || $_GET[ 'tab' ] != $this->tab ) return;
		if ( !isset( $_REQUEST[ 'action' ] ) ) return;
		
		$action = $_REQUEST[ 'action' ];
		$theme_disabled = false;
		if ( 'disallow-update-selected' == $action ) {
			$theme_disabled = true;
		}
		
		//Build Query Args
		$paged = isset( $_GET[ 'paged' ] ) ? absint( $_GET[ 'paged' ] ) : false;
		$query_args = array();
		$query_args[ 'page' ] = $this->slug;
		if ( false !== $paged ) {
			$query_args[ 'paged' ] = $paged;	
		}
		if ( false == $theme_disabled ) {
			$query_args[ 'disabled' ] = 0;
		} else {
			$query_args[ 'disabled' ] = 1;
		}
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
	
	private function save_theme_update_options( $action ) {
		//Check capability
		$capability = 'update_themes'; //On single site, admins can use this, on multisite, only network admins can
		if ( !current_user_can( $capability ) ) return;
		
		$themes = isset( $_REQUEST[ 'checked' ] ) ? (array) $_REQUEST[ 'checked' ] : array();		
		$theme_options = MPSUM_Updates_Manager::get_options( 'themes' );

		switch( $action ) {
			case 'disallow-update-selected':
				foreach( $themes as $theme ) {
					$theme_options[] = $theme;	
				}
				
				break;
			case 'allow-update-selected':
				foreach( $themes as $theme ) {
					if ( ( $key = array_search( $theme, $theme_options ) ) !== false ) {
						unset( $theme_options[ $key ] );
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
		MPSUM_Updates_Manager::update_options( $theme_options, 'themes' ); 	
	}
	
	public function tab_output_themes() {
		if ( isset( $_GET[ 'disabled' ] ) ) {
			$message = __( 'The selected themes have had their upgrades enabled.', 'stops-core-theme-and-plugin-updates' );
			if ( $_GET[ 'disabled' ] == 1 ) {
				$message = __( 'The selected themes have had their upgrades disabled.', 'stops-core-theme-and-plugin-updates' );
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
        <h2><?php esc_html_e( 'Theme Update Options', 'stops-core-theme-and-plugin-updates' ); ?></h2>
        <?php 
	    $theme_table = new MPSUM_Themes_List_Table( $args = array( 'screen' => $this->slug, 'tab' => $this->tab ) );
		$theme_table->prepare_items();
		$theme_table->views();
		$theme_table->display() ?>
        </form>
    <?php
	} //end tab_output_plugins
	
	public function theme_action_links( $settings, $theme ) {
		$stylesheet = $theme->get_stylesheet();
		$theme_options = MPSUM_Updates_Manager::get_options( 'themes' );
		if ( false !== $key = array_search( $stylesheet, $theme_options ) ) {
			$enable_url = add_query_arg( array( 'action' => 'allow-update-selected', '_mpsum' => wp_create_nonce( 'mpsum_theme_update' ), 'checked' => array( $stylesheet ) ) );
			$enable_url = remove_query_arg( 'disabled', $enable_url );
			$settings[] = sprintf( '<a href="%s">%s</a>', esc_url( $enable_url ), esc_html__( 'Allow Updates', 'stops-core-theme-and-plugin-updates' ) );
		} else {
			$disable_url = add_query_arg( array( 'action' => 'disallow-update-selected', '_mpsum' => wp_create_nonce( 'mpsum_theme_update' ), 'checked' => array( $stylesheet ) ) );
			$disable_url = remove_query_arg( 'disabled', $disable_url );
			$settings[] = sprintf( '<a href="%s">%s</a>', esc_url( $disable_url ), esc_html__( 'Disallow Updates', 'stops-core-theme-and-plugin-updates' ) );
		}
		return $settings;	
	}
}