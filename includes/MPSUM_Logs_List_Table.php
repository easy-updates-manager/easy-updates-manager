<?php
/**
 * Easy Updates Manager Logs List Table class.
 *
 * @package WordPress
 * @subpackage MPSUM_List_Table
 * @since 6.0.0
 * @access private
 */
class MPSUM_Logs_List_Table extends MPSUM_List_Table {

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @global object $post_type_object
	 * @global wpdb   $wpdb
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		global $post_type_object, $wpdb;

		parent::__construct( array(
			'singular'=> 'log',			
			'plural' => 'logs',
        ) );
	}

	public function prepare_items() {
		global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();
        $tablename = $wpdb->base_prefix . 'eum_logs';
        $per_page = 50;
		$log_count = $wpdb->get_var( "select count( * ) from $tablename" );
		
		$paged = isset( $_GET[ 'paged' ] ) ? absint( $_GET[ 'paged' ] ) : 0;
		if ( 1 == $paged || 0 == $paged ) {
    		$offset = 0;
		} else {
    		$offset = ( $paged -1 ) * $per_page;
		}
		$query = $wpdb->prepare( "select * from $tablename order by log_id DESC limit %d,%d", $offset, $per_page );
		
		$this->items = $wpdb->get_results( $query );
		
		/* -- Register the Columns -- */
		$this->_column_headers = array( 
			$this->get_columns(),		// columns
			array(),			// hidden
			$this->get_sortable_columns(),	// sortable
		);

		$this->set_pagination_args( array(
			'total_items' => $log_count,
			'per_page' => $per_page
		) );
	}

	/**
	 *
	 * @return array
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'fixed', 'striped' );
	}

	/**
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array( 
    		'user'    => _x( 'User', 'Column header for logs', 'stops-core-theme-and-plugin-updates' ),
		    'name'    => _x( 'Name', 'Column header for logs', 'stops-core-theme-and-plugin-updates' ),
		    'type'    => _x( 'Type', 'Column header for logs', 'stops-core-theme-and-plugin-updates' ),
		    'version' => _x( 'Version', 'Column header for logs', 'stops-core-theme-and-plugin-updates' ),
		    'action'  => _x( 'Action', 'Column header for logs', 'stops-core-theme-and-plugin-updates' ),
		    'status'  => _x( 'Status', 'Column header for logs', 'stops-core-theme-and-plugin-updates' ),
		    'date'    => _x( 'Date', 'Column header for logs', 'stops-core-theme-and-plugin-updates' ),
		    
        );
		return $columns;
	}

	/**
	 * @global WP_Query $wp_query
	 * @global int $per_page
	 * @param array $posts
	 * @param int $level
	 */
	public function display_rows( $posts = array(), $level = 0 ) {
		$records = $this->items;
		foreach ( $this->items as $record ) {
    	    $this->single_row( $record );	
		}
			
	}

	/**
	 *
	 * @param array log record
	 */
	public function single_row( $record ) {
		
	?>
		<tr id="log-<?php echo $record->log_id; ?>">
			<?php
    		foreach( $record as $record_key => $record_data ) {
        		if ( 'log_id' == $record_key ) continue;
        		echo '<td>';
        		switch( $record_key ) {
            		case 'user_id':
            		    if ( 0 == $record_data ) {
                		    echo _x( 'None', 'No user found', 'stops-core-theme-and-plugin-updates' );
            		    } else {
                		    $user = get_user_by( 'id', $record_data );
                		    if ( $user ) {
                    		    echo esc_html( $user->user_nicename );
                		    }
                		    
            		    }
            		    break;
                    case 'name':
                        echo esc_html( $record_data );
                        break;
                    case 'type':
                        if ( 'core' == $record_data ) {
                            echo esc_html( ucfirst( _x( 'core', 'update type', 'stops-core-theme-and-plugin-updates' ) ) );
                        } elseif ( 'translation' == $record_data ) {
                            echo esc_html( ucfirst( _x( 'translation', 'update type', 'stops-core-theme-and-plugin-updates' ) ) );
                        } elseif( 'plugin' == $record_data ) {
                           echo esc_html( ucfirst( _x( 'plugin', 'update type', 'stops-core-theme-and-plugin-updates' ) ) );
                        } elseif( 'theme' == $record_data ) {
                            echo esc_html( ucfirst( _x( 'theme', 'update type', 'stops-core-theme-and-plugin-updates' ) ) );
                        } else {
                            echo esc_html( ucfirst( $record_data ) );
                        }
                        break;
                    case 'version':
                        echo esc_html( $record_data );
                        break;
                    case 'action':
                        if ( 'manual' == $record_data ) {
                            echo esc_html( ucfirst( _x( 'manual', 'update type - manual or automatic updates', 'stops-core-theme-and-plugin-updates' ) ) );
                        } elseif( 'automatic' == $record_data ) {
                            echo esc_html( ucfirst( _x( 'automatic', 'update type - manual or automatic updates', 'stops-core-theme-and-plugin-updates' ) ) );
                        } else {
                            echo esc_html( ucfirst( $record_data ) );
                        }
                        
                        break;
                    case 'status':
                        if ( 1 == $record_data ) {
                            echo esc_html__( 'Success', 'stops-core-theme-and-plugin-updates' );
                        } else {
                            echo esc_html__( 'Failure', 'stops-core-theme-and-plugin-updates' );
                        }
                        break;
                    case 'date':
                        echo esc_html( $record_data );
                        break;
                    default:
                        break;
        		}
        		echo '</td>';
    		}	
    			
            ?>
		</tr>
	<?php
	}
}
