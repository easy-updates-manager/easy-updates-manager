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
	 * Plugin cache for version compares.
	 *
	 * @since 6.4.4
	 * @access private
	 * @var stdClass plugins
	 */
	private $plugins_cache = null;
	
	/**
	 * Theme cache for version compares.
	 *
	 * @since 6.4.4
	 * @access private
	 * @var stdClass $themes
	 */
	private $themes_cache = null;
	
	/**
	 * Holds the WordPress Version.
	 *
	 * @since 6.4.4
	 * @access private
	 * @var string $wp_version
	 */
	private $wp_version = null;
	
	/**
	 * Holds the Translation cache for version compares.
	 *
	 * @since 6.4.4
	 * @access private
	 * @var string $wp_version
	 */
	private $translations_cache = null;
	
	/**
	* Holds version number of the table
	*
	* @since 6.0.0
	* @access static
	* @var string $slug
	*/
	private $version = '1.1.3';
	
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
		
		// Set plugin/theme/wordpress/translation variables for log error checking
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		// Store plugins cache for later use in version compares
		$this->plugins_cache = get_site_transient( 'MPSUM_PLUGINS' );
		if ( false === $this->plugins_cache ) {
			$this->plugins_cache = get_plugins();
			set_site_transient( 'MPSUM_PLUGINS', $this->plugins_cache, 5 * MINUTE_IN_SECONDS );
		}
		
		// Store themes cache for later use in version compares
		$this->themes_cache = get_site_transient( 'MPSUM_THEMES' );
		if ( false === $this->themes_cache ) {
			$this->themes_cache = wp_get_themes();
			set_site_transient( 'MPSUM_THEMES', $this->themes_cache, 5 * MINUTE_IN_SECONDS );
		}
		$this->translations_cache = wp_get_translation_updates();
		include( ABSPATH . WPINC . '/version.php' );
		$this->wp_version = $wp_version;

		add_action( 'automatic_updates_complete', array( $this, 'automatic_updates' ) );
		add_action( 'upgrader_process_complete', array( $this, 'manual_updates' ), 5, 2 );
	
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
					 $status = 0;
					 $version = '';
					 if ( ! is_wp_error( $core->result ) ) {
						 $version = $core->result;
						 
						 if ( $core->result == $this->wp_version || NULL === $core->result) {
							 $version = $this->wp_version;
							 $status = 0;
						 } else {
							 $status = 1;
						 }
					 }
					 $wpdb->insert( 
						 $tablename,
						 array(
							 'name'	   => $core->name,
							 'type'	   => $type,
							 'version_from' => $this->wp_version,
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
							 '%s',
						 )
					 );
					 break;
				case 'plugin':
					 foreach( $results as $plugin ) {
						 
						 $status = 0;
						 $version = isset( $plugin->item->new_version ) ? $plugin->item->new_version : '0.00';

						 // Get older version
						 $plugin_data = $this->plugins_cache[ $plugin->item->plugin ];
						 
						 if ( ! is_wp_error( $plugin->result ) ) {
							 if ( $version == $plugin_data['Version'] || NULL === $plugin->result  ) {
								 $status = 0;
							 } else {
								 $status = 1;
							 }
						 }
						 
						 $name = ( isset( $plugin->name ) && !empty( $plugin->name ) ) ? $plugin->name : $plugin->item->slug;
						 
						 
						 
						 $wpdb->insert( 
							 $tablename,
							 array(
								 'name'	        => $name,
								 'type'	        => $type,
								 'version_from' => $plugin_data['Version'],
								 'version'      => $version,
								 'action'       => 'automatic',
								 'status'       => $status,
								 'date'	        => current_time( 'mysql' ),
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
					 delete_site_transient( 'MPSUM_PLUGINS' );
					 break;
				case 'theme':
					foreach( $results as $theme ) {
						$status = 0;
												
						// Retrive Older Version
						$old_theme_version = $this->themes_cache[ $theme->item->theme ];
				 		$from_version = $old_theme_version->get( 'Version' );
				 		
						 if ( ! is_wp_error( $theme->result ) ) {
							 if ( $from_version == $theme->item->new_version || NULL === $theme->result  ) {
								 $version = $from_version;
								 $status = 0;
							 } else {
								 $version = $theme->item->new_version;
								 $status = 1;
							 }
						 }
						
						 $wpdb->insert( 
							 $tablename,
							 array(
								 'name'	        => $theme->name,
								 'type'	        => $type,
								 'version_from' => $from_version,
								 'version'      => $version,
								 'action'       => 'automatic',
								 'status'       => $status,
								 'date'	        => current_time( 'mysql' ),
							 ),
							 array(
								 '%s',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
								 '%s',
							 )
						 );
					 }
					 delete_site_transient( 'MPSUM_THEMES' );
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
								 'name'         => $name . ' (' . $translation->item->language . ')',
								 'type'         => $type,
								 'version_from' => $this->get_version_for_type( $type, $slug ),
								 'version'      => $version,
								 'action'       => 'automatic',
								 'status'       => $status,
								 'date'	        => current_time( 'mysql' ),
							 ),
							 array(
								 '%s',
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
	
	/**
	 * Get the version of a translation item to be updated
	 *
	 * @since 6.4.4
	 * @access private
	 *
	 * @param	string type of translation update
	 * @param	string $slug of item
	 * @return string The name of the item being updated.
	 */
	private function get_version_for_type( $type, $slug ) {
		$translation_updates = $this->translations_cache;
		switch ( $type ) {
			case 'core':
				return $this->wp_version;
				break;
			case 'translation':
			case 'theme':
			case 'plugin':
				if ( is_array( $translation_updates ) && ! empty( $translation_updates ) ) {
					foreach( $translation_updates as $translation_update ) {
						if ( $slug == $translation_update->slug ) {
							return $translation_update->version;
						}
					}
				}
			
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
						 'user_id'      => $user_id,
						 'name'	        => 'WordPress ' . $wp_version,
						 'type'	        => $options[ 'type' ],
						 'version_from' => $this->wp_version,
						 'version'      => $wp_version,
						 'action'       => 'manual',
						 'status'       => 1,
						 'date'	        => current_time( 'mysql' ),
					 ),
					 array(
						 '%d',
						 '%s',
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
				if ( ! empty( $this->plugins_cache ) && isset( $options[ 'plugins' ] ) && !empty( $options[ 'plugins' ] ) ) {
					$plugins = get_plugins();
					foreach( $options[ 'plugins' ] as $plugin ) {
						$plugin_data = isset( $plugins[ $plugin ] ) ? $plugins[ $plugin ] : false;
						$version_from = '';
						if ( isset( $this->plugins_cache[ $plugin ] ) ) {
							$version_from = $this->plugins_cache[ $plugin ][ 'Version' ];
						}
						
						if ( false !== $plugin_data ) {
							$status = ( $plugin_data[ 'Version' ] == $version_from ) ? 0 : 1;
							$wpdb->insert( 
								 $tablename,
								 array(
									 'user_id'      => $user_id,
									 'name'	        => $plugin_data[ 'Name' ],
									 'type'	        => $options[ 'type' ],
									 'version_from' => $version_from,
									 'version'      => $plugin_data[ 'Version' ],
									 'action'       => 'manual',
									 'status'       => $status,
									 'date'	        => current_time( 'mysql' ),
								 ),
								 array(
									 '%d',
									 '%s',
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
				delete_site_transient( 'MPSUM_PLUGINS' );
				break;
			case 'theme':
				if ( isset( $options[ 'themes' ] ) && !empty( $options[ 'themes' ] ) ) {
					
					foreach( $options[ 'themes' ] as $theme ) {
						$theme_data = wp_get_theme( $theme );
					
						if ( $theme_data->exists() ) {
							
							$version_from = '';
							if ( isset( $this->themes_cache[ $theme ] ) ) {
								$version_from = $this->themes_cache[ $theme ]->get( 'Version' );
							}
							$status = ( $version_from == $theme_data->get( 'Version' ) ) ? 0 : 1;
							$wpdb->insert( 
								 $tablename,
								 array(
									 'user_id'      => $user_id,
									 'name'	        => $theme_data->get( 'Name' ),
									 'type'	        => $options[ 'type' ],
									 'version_from' => $version_from,
									 'version'      => $theme_data->get( 'Version' ),
									 'action'       => 'manual',
									 'status'       => $status,
									 'date'	        => current_time( 'mysql' ),
								 ),
								 array(
									 '%d',
									 '%s',
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
				delete_site_transient( 'MPSUM_THEMES' );
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
								 'user_id'      => $user_id,
								 'name'	        => $name . ' (' . $translation[ 'language' ] . ')',
								 'type'	        => $translation[ 'type' ],
								 'version_from' => $this->get_version_for_type( $translation[ 'type' ], $slug ),
								 'version'      => $version,
								 'action'       => 'manual',
								 'status'       => $status,
								 'date'	        => current_time( 'mysql' ),
							 ),
							 array(
								 '%d',
								 '%s',
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
						version_from VARCHAR(255) NOT NULL,
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
