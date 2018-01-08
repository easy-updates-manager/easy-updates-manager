<?php
/**
 * Easy Updates Manager REST API
 *
 * Initializes the REST API
 *
 * @since 7.0.0
 *
 * @package WordPress
 */
class MPSUM_Rest {

	/**
	* Holds the class instance.
	*
	* @since 7.0.0
	* @access static
	* @var MPSUM_Rest $instance
	*/
	private static $instance = null;

	/**
	* Set a class instance.
	*
	* Set a class instance.
	*
	* @since 6.0.0
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
	* @since 6.0.0
	* @access private
	*
	*/
	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );

	} //end constructor

	public function rest_api_init() {
		register_rest_route(
			'eum/v1',
			'/get/',
			array(
				'methods' => 'get',
				'callback' => array( $this, 'return_options' ),
				'permission_callback' => array( $this, 'permission_callback' )
			)
		);
	}

	public function return_options() {

		// Get options
		$options = MPSUM_Updates_Manager::get_options( 'core', true );

		// return
		return $options;
	}

	public function permission_callback() {
		return true; // todo use cookie based authentication
		if ( current_user_can( 'install_plugins' ) ) {
			return true;
		}
		return false;
	}
}
