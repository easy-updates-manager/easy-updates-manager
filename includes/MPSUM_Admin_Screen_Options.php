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
	
	private function set_screen_options() {
		$args = array(
			'label' => __( 'Items Per Page', 'stops-core-theme-and-plugin-updates' ),
			'default' => 100,
			'option' => 'mpsum_items_per_page'
		);

		add_screen_option( 'per_page', $args );
	}
	
}
