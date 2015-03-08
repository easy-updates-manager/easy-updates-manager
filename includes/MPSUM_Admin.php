<?php
class MPSUM_Admin {
	private static $instance = null;
	private static $url = '';
	private static $slug = 'mpsum-update-options';
	
	//Singleton
	public static function run() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	} //end get_instance	
	
	private function __construct() {
		add_action( 'init', array( $this, 'init' ), 9 );
	} //end constructor
	
	public static function get_url() {
		$url = self::$url;
		if ( empty( $url ) ) {
			if ( is_multisite() ) {
				$url = add_query_arg( array( 'page' => self::get_slug() ), network_admin_url( 'update-core.php' ) );	
			} else {
				$url = add_query_arg( array( 'page' => self::get_slug() ), admin_url( 'index.php' ) );
			}
			self::$url = $url;
		}
		return $url;
	}
	
	public static function get_slug() {
		return self::$slug;
	}
	
	public function init() {
		
		//Plugin and Theme actions
		if ( is_multisite() ) {
			add_action( 'network_admin_menu', array( $this, 'init_network_admin_menus' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'init_single_site_admin_menus' ) );
		}
		
		new MPSUM_Admin_Plugins( self::get_slug() );
		new MPSUM_Admin_Themes( self::get_slug() );
		new MPSUM_Admin_Core( self::get_slug() );
		
	}	
	
	public function init_network_admin_menus() {
		add_submenu_page( 'update-core.php', __( 'Update Options', 'stops-core-theme-and-plugin-updates' ) , __( 'Update Options', 'stops-core-theme-and-plugin-updates' ), 'update_core', self::get_slug(), array( $this, 'output_admin_interface' ) );
	}
	
	public function init_single_site_admin_menus() {
		add_dashboard_page( __( 'Update Options', 'stops-core-theme-and-plugin-updates' ) , __( 'Update Options', 'stops-core-theme-and-plugin-updates' ), 'update_core', self::get_slug(), array( $this, 'output_admin_interface' ) );	
	}
	
	public function output_admin_interface() {
		?>
		<div class="wrap">
			<h2>
				<?php echo esc_html_e( 'Update Options', 'stops-core-theme-and-plugin-updates' ); ?>
			</h2>
			<?php
			$tabs = 
			array(
				array(
					'url' => add_query_arg( array( 'tab' => 'main' ), self::get_url() ), /* URL to the tab */
					'label' => esc_html__( 'General', 'stops-core-theme-and-plugin-updates' ),
					'get' => 'main' /*$_GET variable*/,
					'action' => 'mpsum_admin_tab_main' /* action variable in do_action */
				),
				array(
					'url' => add_query_arg( array( 'tab' => 'plugins' ), self::get_url() ), /* URL to the tab */
					'label' => esc_html__( 'Plugins', 'stops-core-theme-and-plugin-updates' ),
					'get' => 'plugins' /*$_GET variable*/,
					'action' => 'mpsum_admin_tab_plugins' /* action variable in do_action */
				),
				array(
					'url' => add_query_arg( array( 'tab' => 'themes' ), self::get_url() ), /* URL to the tab */
					'label' => esc_html__( 'Themes', 'stops-core-theme-and-plugin-updates' ),
					'get' => 'themes' /*$_GET variable*/,
					'action' => 'mpsum_admin_tab_themes' /* action variable in do_action */
				),
				array(
					'url' => add_query_arg( array( 'tab' => 'advanced' ), self::get_url() ), /* URL to the tab */
					'label' => esc_html__( 'Advanced', 'stops-core-theme-and-plugin-updates' ),
					'get' => 'advanced' /*$_GET variable*/,
					'action' => 'mpsum_admin_tab_advanced' /* action variable in do_action */
				)
			);
			$tabs_count = count( $tabs );
			if ( $tabs && !empty( $tabs ) )  {
				$tab_html .=  '<h2 class="nav-tab-wrapper">';
				$active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : 'main';
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
					do_action( $do_action );	
				}
			}	
			?>
			
		</div><!-- .wrap -->
		<?php
	} //end output_admin_interface
}