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
		register_rest_route(
			'eum/v1',
			'/save/(?P<id>[a-zA-Z0-9-]+)/(?P<value>[a-zA-Z0-9-]+)',
			array(
				'methods' => 'post',
				'callback' => array( $this, 'save_options' ),
				'permission_callback' => array( $this, 'permission_callback' ),
				'args' => array(
					'id' => array(
						'validate_callback' => 'sanitize_text_field',
					),
					'value' => array(
						'validate_callback' => 'sanitize_text_field',
					)
				)
			)
		);
	}

	public function save_options( $request ) {

		// Get options
		$options = MPSUM_Updates_Manager::get_options( 'core', true );
		if ( empty( $options ) ) {
			$options = MPSUM_Admin_Core::get_defaults();
		}
		error_log( print_r( $request->get_params(), true ) );
		return $options;
	}

	public function return_options() {

		// Get options
		$options = MPSUM_Updates_Manager::get_options( 'core', true );
		if ( empty( $options ) ) {
			$options = MPSUM_Admin_Core::get_defaults();
		}

		// Set automatic updates defaults if none is selected
		if ( ! isset( $options[ 'automatic_updates' ] ) || 'unset' === $options[ 'automatic_updates' ] ) {

			// Check to see if automatic updates are on
			// Prepare for mad conditionals
			if ( 'off' == $options[ 'automatic_development_updates' ] && 'on' == $options[ 'automatic_major_updates' ] && 'on' == $options[ 'automatic_minor_updates' ] && 'on' == $options[ 'automatic_plugin_updates' ] && 'on' == $options[ 'automatic_theme_updates' ] && 'on' == $options[ 'automatic_translation_updates' ] ) {
				$options[ 'automatic_updates' ] = 'on';
			} elseif( 'off' == $options[ 'automatic_development_updates' ] && 'off' == $options[ 'automatic_major_updates' ] && 'off' == $options[ 'automatic_minor_updates' ] && 'off' == $options[ 'automatic_plugin_updates' ] && 'off' == $options[ 'automatic_theme_updates' ] && 'off' == $options[ 'automatic_translation_updates' ] ) {
				$options[ 'automatic_updates' ] = 'off';
			} elseif( 'off' == $options[ 'automatic_development_updates' ] && 'off' == $options[ 'automatic_major_updates' ] && 'on' == $options[ 'automatic_minor_updates' ] && 'default' == $options[ 'automatic_plugin_updates' ] && 'default' == $options[ 'automatic_theme_updates' ] && 'on' == $options[ 'automatic_translation_updates' ] ) {
				$options[ 'automatic_updates' ] = 'default';
			} else {
				$options[ 'automatic_updates' ] = 'custom';
			}
		}

		// return
		return $options;
	}

	public function permission_callback() {

		if ( current_user_can( 'install_plugins' ) ) {
			return true;
		}
		return false;
	}
}
