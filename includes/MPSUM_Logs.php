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
	* Holds the slug to the admin panel page
	*
	* @since 6.0.0
	* @access static
	* @var string $slug
	*/
	private static $slug = 'mpsum-update-options';
	
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
	
	} //end constructor
	
	public function automatic_updates( $update_results ) {
    	error_log( print_r( $update_results, true ) );
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
                    	    'name'    => $core->name,
                    	    'type'    => $type,
                    	    'version' => $version,
                    	    'action'  => 'automatic',
                    	    'status'  => $status,
                    	    'date'    => current_time( 'mysql' ),
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
                	    $version = ( 1 == $status ) ? $plugin->item->new_version : '';
                	    $wpdb->insert( 
                    	    $tablename,
                    	    array(
                        	    'name'    => $plugin->name,
                        	    'type'    => $type,
                        	    'version' => $version,
                        	    'action'  => 'automatic',
                        	    'status'  => $status,
                        	    'date'    => current_time( 'mysql' ),
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
                	    $status = is_wp_error( $theme->result ) ? 0: 1;
                	    $version = ( 1 == $status ) ? $theme->item->new_version : '';
                	    $wpdb->insert( 
                    	    $tablename,
                    	    array(
                        	    'name'    => $theme->name,
                        	    'type'    => $type,
                        	    'version' => $version,
                        	    'action'  => 'automatic',
                        	    'status'  => $status,
                        	    'date'    => current_time( 'mysql' ),
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
                	    $version = ( 1 == $status ) ? $translation->item->new_version : '';
                	    $wpdb->insert( 
                    	    $tablename,
                    	    array(
                        	    'name'    => $translation->name,
                        	    'type'    => $type,
                        	    'version' => $version,
                        	    'action'  => 'automatic',
                        	    'status'  => $status,
                        	    'date'    => current_time( 'mysql' ),
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