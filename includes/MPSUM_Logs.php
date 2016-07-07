<?php
/**
 * Easy Updates Manager log controller
 *
 * Initializes the log table and sets up actions/events
 *
 * @since 6.0.0
 *
 * @package WordPress
 */
class MPSUM_Logs {
	
	/**
	* Holds the class instance.
	*
	* @since 6.0.0
	* @access static
	* @var MPSUM_Log $instance
	*/
	private static $instance = null;
	
	/**
	* Holds version number of the table
	*
	* @since 6.0.0
	* @access static
	* @var string $slug
	*/
	private $version = '1.0.0';
	
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
	* @since 6.0.0
	* @access private
	*
	*/
	private function __construct() {
		$table_version = get_site_option( 'mpsum_log_table_version', '0' );
		if ( version_compare( $table_version, $this->version ) < 0 ) {
			$this->build_table();
			update_site_option( 'mpsum_log_table_version', $this->version );
		}
		
		add_action( 'automatic_updates_complete', array( $this, 'automatic_updates' ) );
		add_action( 'upgrader_process_complete', array( $this, 'manual_updates' ), 10, 2 );
	
	} //end constructor
	
	/**
	* automatic_updates
	*
	* Log automatic updates
	*
	* @since 6.0.0
	* @access public
	*
	*/
	public function automatic_updates( $update_results ) {
		global $wpdb;
		$tablename = $wpdb->base_prefix . 'eum_logs';
		if ( empty( $update_results ) ) return;
		
		foreach( $update_results as $type => $results ) {
			switch( $type ) {
				case 'core':
					 $core = $results[ 0 ];
					 $status = is_wp_error( $core->result ) ? 0: 1;
					 $version = ( 1 == $status ) ? $core->result : '';
					 $wpdb->insert( 
						 $tablename,
						 array(
							 'name'	   => $core->name,
							 'type'	   => $type,
							 'version' => $version,
							 'action'  => 'automatic',
							 'status'  => $status,
							 'date'	   => current_time( 'mysql' ),
						 ),
						 array(
							 '%s',
							 '%s',
							 '%s',
							 '%s',
							 '%s',
							 '%s',
						 )
					 );
					 break;
				case 'plugin':
					 foreach( $results as $plugin ) {
						 $status = is_wp_error( $plugin->result ) ? 0: 1;
						 $version = isset( $plugin->item->new_version ) ? $plugin->item->new_version : '0.00';
						 $name = ( isset( $plugin->name ) && !empty( $plugin->name ) ) ? $plugin->name : $plugin->item->slug;
						 $wpdb->insert( 
							 $tablename,
							 array(
								 'name'	   => $name,
								 'type'	   => $type,
								 'version' => $version,
								 'action'  => 'automatic',
								 'status'  => $status,
								 'date'	   => current_time( 'mysql' ),
							 ),
							 array(
								 '%s',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
							 )
						 );
					 }
					 break;
				case 'theme':
					foreach( $results as $theme ) {
						 $status = ( is_wp_error( $theme->result ) || empty( $theme->result ) ) ? 0: 1;
						 if ( 0 == $status ) {
					 		  $theme_data_from_cache = wp_get_themes();
					 		  $theme_data = $theme_data_from_cache[ $theme->item->theme ];
					 		  $version = $theme_data->get( 'Version' );
						 } else {
					 		  $version = $theme->item->new_version;
						 }
						 $wpdb->insert( 
							 $tablename,
							 array(
								 'name'	   => $theme->name,
								 'type'	   => $type,
								 'version' => $version,
								 'action'  => 'automatic',
								 'status'  => $status,
								 'date'	   => current_time( 'mysql' ),
							 ),
							 array(
								 '%s',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
							 )
						 );
					 }
					break;
				case 'translation':
					foreach( $results as $translation ) {
						
						 $status = is_wp_error( $translation->result ) ? 0: 1;
						 $version = ( 1 == $status ) ? $translation->item->version : '';
						 $slug = $translation->item->slug;
						 $name = $this->get_name_for_update( $translation->item->type, $translation->item->slug );
						 $wpdb->insert( 
							 $tablename,
							 array(
								 'name'	   => $name . ' (' . $translation->item->language . ')',
								 'type'	   => $type,
								 'version' => $version,
								 'action'  => 'automatic',
								 'status'  => $status,
								 'date'	   => current_time( 'mysql' ),
							 ),
							 array(
								 '%s',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
							 )
						 );
					 }
					break;
			}
		}	  	
	}
	
	/**
	 * Get the name of an translation item being updated.
	 *
	 * @since 6.0.3
	 * @access private
	 *
	 * @param	string type of translation update
	 * @param	string $slug of item
	 * @return string The name of the item being updated.
	 */
	private function get_name_for_update( $type, $slug ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		switch ( $type ) {
			case 'core':
				return 'WordPress'; // Not translated

			case 'theme':
				$theme = wp_get_theme( $slug );
				if ( $theme->exists() )
					return $theme->Get( 'Name' );
				break;
			case 'plugin':
				$plugin_data = get_plugins( '/' . $slug );
				$plugin_data = reset( $plugin_data );
				if ( $plugin_data )
					return $plugin_data['Name'];
				break;
		}
		return '';
	}
	
	public function manual_updates( $upgrader_object, $options ) {
		if ( !isset( $options[ 'action' ] ) || 'update' !== $options[ 'action' ] ) return;
		global $wpdb;
		$tablename = $wpdb->base_prefix . 'eum_logs';
		$user_id = get_current_user_id();
		if ( 0 == $user_id ) return; // If there is no user, this is not a manual update
		
		
		switch( $options[ 'type' ] ) {
			case 'core':
				 include( ABSPATH . WPINC . '/version.php' );
				 $wpdb->insert( 
					 $tablename,
					 array(
						 'user_id' => $user_id,
						 'name'	   => 'WordPress ' . $wp_version,
						 'type'	   => $options[ 'type' ],
						 'version' => $wp_version,
						 'action'  => 'manual',
						 'status'  => 1,
						 'date'	   => current_time( 'mysql' ),
					 ),
					 array(
						 '%d',
						 '%s',
						 '%s',
						 '%s',
						 '%s',
						 '%s',
						 '%s',
					 )
				 );
				 break;
			case 'plugin':
				$plugins = array();
				if ( ! function_exists( 'get_plugins' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				$plugins_from_cache = get_plugins();
				wp_clean_plugins_cache();
				$plugins = get_plugins();
				if ( !empty( $plugins ) && isset( $options[ 'plugins' ] ) && !empty( $options[ 'plugins' ] ) ) {
					foreach( $options[ 'plugins' ] as $plugin ) {
						$plugin_data = isset( $plugins[ $plugin ] ) ? $plugins[ $plugin ] : false;
						$plugin_data_cache = isset( $plugins_from_cache[ $plugin ] ) ? $plugins_from_cache[ $plugin ] : false;
						if ( false !== $plugin_data && false !== $plugin_data_cache ) {
							$status = ( $plugin_data[ 'Version' ] == $plugin_data_cache[ 'Version' ] ) ? 0 : 1;
							$wpdb->insert( 
								 $tablename,
								 array(
									 'user_id' => $user_id,
									 'name'	   => $plugin_data[ 'Name' ],
									 'type'	   => $options[ 'type' ],
									 'version' => $plugin_data[ 'Version' ],
									 'action'  => 'manual',
									 'status'  => $status,
									 'date'	   => current_time( 'mysql' ),
								 ),
								 array(
									 '%d',
									 '%s',
									 '%s',
									 '%s',
									 '%s',
									 '%s',
									 '%s',
								 )
							 );
						}
					}
				}
				break;
			case 'theme':
				if ( isset( $options[ 'themes' ] ) && !empty( $options[ 'themes' ] ) ) {
					$theme_data_from_cache = wp_get_themes();
					wp_clean_themes_cache();
					foreach( $options[ 'themes' ] as $theme ) {
						$theme_data = wp_get_theme( $theme );
						$theme_from_cache = $theme_data_from_cache[ $theme ];
						if ( $theme_data->exists() ) {
							$status = ( $theme_from_cache->get( 'Version' ) == $theme_data->get( 'Version' ) ) ? 0 : 1;
							$wpdb->insert( 
								 $tablename,
								 array(
									 'user_id' => $user_id,
									 'name'	   => $theme_data->get( 'Name' ),
									 'type'	   => $options[ 'type' ],
									 'version' => $theme_data->get( 'Version' ),
									 'action'  => 'manual',
									 'status'  => $status,
									 'date'	   => current_time( 'mysql' ),
								 ),
								 array(
									 '%d',
									 '%s',
									 '%s',
									 '%s',
									 '%s',
									 '%s',
									 '%s',
								 )
							 );
						}
					}
				}
				break;
			case 'translation':
					foreach( $options[ 'translations' ] as $translation ) {
						
						 $status = 1;
						 $version = $translation[ 'version' ];
						 $slug = $translation[ 'slug' ];
						 $name = $this->get_name_for_update( $translation[ 'type' ], $slug );
						 $wpdb->insert( 
							 $tablename,
							 array(
								 'user_id' => $user_id,
								 'name'	   => $name . ' (' . $translation[ 'language' ] . ')',
								 'type'	   => $translation[ 'type' ],
								 'version' => $version,
								 'action'  => 'manual',
								 'status'  => $status,
								 'date'	   => current_time( 'mysql' ),
							 ),
							 array(
								 '%d',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
							 )
						 );
					 }
					break;
			   
		}
	}
	
	/**
	* Creates the log table
	*
	* Creates the log table
	*
	* @since 6.0.0
	* @access private
	*
	*/
	private function build_table() {
		global $wpdb;
		$tablename = $wpdb->base_prefix . 'eum_logs';
		
		// Get collation - From /wp-admin/includes/schema.php
		$charset_collate = '';
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";

		$sql = "CREATE TABLE {$tablename} (
						log_id BIGINT(20) NOT NULL AUTO_INCREMENT,
						user_id BIGINT(20) NOT NULL DEFAULT 0,
						name VARCHAR(255) NOT NULL,
						type VARCHAR(255) NOT NULL,
						version VARCHAR(255) NOT NULL,
						action VARCHAR(255) NOT NULL,
						status VARCHAR(255) NOT NULL,
						date DATETIME NOT NULL,
						PRIMARY KEY  (log_id) 
						) {$charset_collate};";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
	}
	
	/**
	* Clears the log table
	*
	* Clears the log table
	*
	* @since 6.0.0
	* @access static
	*
	*/
	public static function clear() {
		global $wpdb;
		$tablename = $wpdb->base_prefix . 'eum_logs';
		$sql = "delete from $tablename";
		$wpdb->query( $sql );
	}
	
	/**
	* Drops the log table
	*
	* Drops the log table
	*
	* @since 6.0.0
	* @access static
	*
	*/
	public static function drop() {
		global $wpdb;
		$tablename = $wpdb->base_prefix . 'eum_logs';
		$sql = "drop table if exists $tablename";
		$wpdb->query( $sql );
	}
}
