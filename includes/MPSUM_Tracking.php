<?php
/**
 * Tracks the data
 *
 * Tracks the data sent to keen.io.
 *
 * @since 6.4.0
 *
 * @package WordPress
 */
class MPSUM_Tracking {
		
	/**
	* Class constructor.
	*
	* Initialize the class
	*
	* @since 6.4.0
	* @access public
	*
	* @param string $slug Slug to the admin panel page
	*/
	public function __construct( $slug = '' ) {
		add_action( 'eum-monthly', array( $this, 'send' ) );
	}
	
	
		
	public static function enable_cron() {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'eum-monthly', 'eum-monthly' );
	}
	
	public static function disable_cron() {
		wp_clear_scheduled_hook( 'eum-monthly' );
	}
	
	public static function send() {
				
		// Gather the options
		$options = MPSUM_Updates_Manager::get_options( 'core', true );
		if ( empty( $options ) ) return;
		
		// Strip out info
		unset( $options[ 'email_addresses' ] );
		$options[ 'wordpress_version' ] = get_bloginfo('version');
		$options[ 'php_version' ] = phpversion();
		
		
		$bucket = $options;
		
		$keen_url = 'https://api.keen.io/3.0/projects/592e7be03d5e1597cc43a83e/events/installs?api_key=7d2b3f5d7f45232c046805c5fb371657af9736a30eaa6b095de65013145980a4ce08deef6c5b1ce0242db46a2302f167046052443351a96f43786799fe0a6b39cee3a22eb675fb4194413b44b0ff7399a1c1c9bc7e3f8afaa8e4ccbdac3513e1';
		
		$headers = array( 
			'Authorization' => 'FCD745F017F92994C88E6A3F3E6B704C81A754DFF6AFE5794C721374B413825B',
			'Content-Type' => 'application/json',
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $keen_url);
		curl_setopt($ch, CURLOPT_HTTPGET, 1);
		curl_setopt($ch, CURLOPT_HEADER, $headers );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		    'Content-Type: application/json',                                                                                
		) );
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $bucket ) );
		curl_exec( $ch ); 
	}
}
