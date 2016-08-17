<?php
/**
 * Screen options screen for Easy Updates Manager
 *
 * Initializes and outputs the screen options screen for the plugin.
 *
 * @since 6.2.0
 *
 * @package WordPress
 */
class MPSUM_Admin_Screen_Options {
	
	/**
	* Holds the class instance.
	*
	* @since 6.2.0
	* @access static
	* @var MPSUM_Admin $instance
	*/
	
	private static $instance = null;
	
	/**
	* Class constructor.
	*
	* Initialize the class
	*
	* @since 6.2.0
	* @access public
	*
	*/
	private function __construct() {
		$this->set_screen_options();
	} //end constructor
	
	/**
	* Save screen option.
	*
	* Save screen option.
	*
	* @since 6.2.0 
	* @access static
	*
	* @param string status
	* @param string option Option name
	* @param string option Option value
	*/
	public static function save_options( $status, $option, $value ) {
		if ( 'mpsum_items_per_page' == $option ) {
			return $value;
		}
		return $status;
	}
	
	/**
	* Set a class instance.
	*
	* Set a class instance.
	*
	* @since 6.2.0 
	* @access static
	*
	*/
	public static function run() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	} //end get_instance
	
	/**
	* Set screen options for items per page.
	*
	* Set screen options for items per page.
	*
	* @since 6.2.0 
	* @access private
	*
	*/
	private function set_screen_options() {
		$args = array(
			'label' => __( 'Items Per Page', 'stops-core-theme-and-plugin-updates' ),
			'default' => 100,
			'option' => 'mpsum_items_per_page'
		);

		add_screen_option( 'per_page', $args );
	}
	
	/**
	* Save dashboard screen options.
	*
	* Save dashboard screen options.
	*
	* @since 6.2.0 
	* @access static
	*
	*/
	public static function maybe_save_dashboard_screen_option() {
		if ( isset( $_REQUEST[ 'mpsum_dashboard' ] ) && isset( $_REQUEST[ 'screenoptionnonce' ] ) ) {
			if ( ! wp_verify_nonce( $_REQUEST[ 'screenoptionnonce' ], 'screen-options-nonce' ) ) {
				return;
			}
			$user_id = get_current_user_id();
			$dashboard = sanitize_text_field( $_REQUEST[ 'mpsum_dashboard' ] );
			if ( 'on' !== $dashboard ) {
				$dashboard = 'off';
			}
			update_user_meta( $user_id, 'mpsum_dashboard', $dashboard );
		}
	}
	
}
