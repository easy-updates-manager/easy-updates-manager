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
	
	private $url = '';

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

		parent::__construct( array(
			'singular'=> 'log',			
			'plural' => 'logs',
        ) );
        
        $this->url = add_query_arg( array( 'tab' => 'logs' ), MPSUM_Admin::get_url() );
	}

	public function prepare_items() {
		global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();
        $tablename = $wpdb->base_prefix . 'eum_logs';
        
        // Get logs per page
		$user_id = get_current_user_id();
		$per_page = get_user_meta( $user_id, 'mpsum_items_per_page', true );
		if ( ! is_numeric( $per_page ) ) {
			$per_page = 100;
		}
        
		$log_count = $wpdb->get_var( "select count( * ) from $tablename" );
		
		$paged = isset( $_GET[ 'paged' ] ) ? absint( $_GET[ 'paged' ] ) : 0;
		if ( 1 == $paged || 0 == $paged ) {
    		$offset = 0;
		} else {
    		$offset = ( $paged -1 ) * $per_page;
		}
		
		$month = isset( $_GET[ 'm' ] ) ? absint( $_GET[ 'm' ] ) : 0;
		
		$where = '';
		if ( isset( $_GET[ 'm' ] ) && strlen( $_GET[ 'm' ] ) > 4 ) {
			$where .= " AND YEAR($tablename.date)=" . substr($month, 0, 4);
			if ( strlen( $month ) > 5 ) {
				$where .= " AND MONTH($tablename.date)=" . substr( $month, 4, 2 );
			}
		}
		if ( isset( $_GET[ 'status' ] ) && 'all' !== $_GET[ 'status' ] ) {
			$where .= $wpdb->prepare( " and $tablename.status = %d ", absint( $_GET[ 'status' ] ) );
		}
		
		if ( isset( $_GET[ 'action' ] ) && 'all' !== $_GET[ 'action' ] ) {
			$where .= $wpdb->prepare( " and $tablename.action = %s ", sanitize_text_field( $_GET[ 'action' ] ) );
		}
		
		if ( isset( $_GET[ 'type' ] ) && 'all' !== $_GET[ 'type' ] ) {
			$where .= $wpdb->prepare( " and $tablename.type = %s ", sanitize_text_field( $_GET[ 'type' ] ) );
		}
		
		$select = "select * from $tablename WHERE 1=1";
		$orderby = " order by log_id DESC";
		$limit = " limit %d,%d";
		$query = $select . $where . $orderby . $limit;
		
		$query = $wpdb->prepare( $query, $offset, $per_page );
		
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
	 * Display a monthly dropdown for filtering items
	 *
	 * @since 6.0.0
	 * @access protected
	 *
	 * @global wpdb      $wpdb
	 *
	 * @param string $post_type
	 */
	protected function months_dropdown( $post_type ) {
		global $wpdb, $wp_locale;
		$tablename = $wpdb->base_prefix . 'eum_logs';
		$query = "SELECT DISTINCT YEAR( date ) AS year, MONTH( date ) AS month FROM $tablename ORDER BY date DESC";

		$months = $wpdb->get_results( $query );

		$month_count = count( $months );
		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;
		
		$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
?>
		<label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date', 'stops-core-theme-and-plugin-updates' ); ?></label>
		<select name="m" id="filter-by-date">
			<option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates', 'stops-core-theme-and-plugin-updates' ); ?></option>
<?php
		foreach ( $months as $arc_row ) {
			if ( 0 == $arc_row->year )
				continue;

			$month = zeroise( $arc_row->month, 2 );
			$year = $arc_row->year;

			printf( "<option %s value='%s'>%s</option>\n",
				selected( $m, $year . $month, false ),
				esc_attr( $arc_row->year . $month ),
				/* translators: 1: month name, 2: 4-digit year */
				sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
			);
		}
?>
		</select>
<?php
	}
	
	private function type_dropdown() {
		$type = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : 'all';
		?>
		<label for="filter-by-type" class="screen-reader-text"><?php _e( 'Filter by Upgrade Type', 'stops-core-theme-and-plugin-updates' ); ?></label>
		<select name="type" id="filter-by-type">
			<option<?php selected( $type, 'all' ); ?> value="all"><?php echo _x( 'All Types', 'Upgrade types: translation, core, plugin, theme', 'stops-core-theme-and-plugin-updates' ); ?></option>
			<option<?php selected( $type, 'core' ); ?> value="core"><?php echo _x( 'Core', 'Show WordPress core updates', 'stops-core-theme-and-plugin-updates' ); ?></option>
			<option<?php selected( $type, 'plugin' ); ?> value="plugin"><?php echo _x( 'Plugins', 'Show WordPress Plugin updates', 'stops-core-theme-and-plugin-updates' ); ?></option>
			<option<?php selected( $type, 'theme' ); ?> value="theme"><?php echo _x( 'Themes', 'Show WordPress Theme updates', 'stops-core-theme-and-plugin-updates' ); ?></option>
			<option<?php selected( $type, 'translation' ); ?> value="translation"><?php echo _x( 'Translations', 'Show WordPress translation updates', 'stops-core-theme-and-plugin-updates' ); ?></option>
		</select>
		<?php
	}
	
	private function status_dropdown() {
		$status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : 'all';
		?>
		<label for="filter-by-success" class="screen-reader-text"><?php _e( 'Filter by Status', 'stops-core-theme-and-plugin-updates' ); ?></label>
		<select name="status" id="filter-by-success">
			<option<?php selected( $status, 'all' ); ?> value="all"><?php _e( 'All Statuses', 'stops-core-theme-and-plugin-updates' ); ?></option>
			<option<?php selected( $status, 1 ); ?> value="1"><?php echo _x( 'Success', 'Show status updates that are successful', 'stops-core-theme-and-plugin-updates' ); ?></option>
			<option<?php selected( $status, 0 ); ?> value="0"><?php echo _x( 'Failures', 'Show status updates that are not successful', 'stops-core-theme-and-plugin-updates' ); ?></option>
		</select>
		<?php
	}
	
	private function action_dropdown() {
		$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'all';
		?>
		<label for="filter-by-action" class="screen-reader-text"><?php _e( 'Filter by Action', 'stops-core-theme-and-plugin-updates' ); ?></label>
		<select name="action" id="filter-by-action">
			<option<?php selected( $action, 'all' ); ?> value="all"><?php _e( 'All Actions', 'stops-core-theme-and-plugin-updates' ); ?></option>
			<option<?php selected( $action, 'automatic' ); ?> value="automatic"><?php echo _x( 'Automatic Updates', 'Show log items that are automatic updates only', 'stops-core-theme-and-plugin-updates' ); ?></option>
			<option<?php selected( $action, 'manual' ); ?> value="manual"><?php echo _x( 'Manual Updates', 'Show log items that are manual updates only', 'stops-core-theme-and-plugin-updates' ); ?></option>
		</select>
		<?php
	}
	
	/**
	 * @global int $cat
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		global $cat;
?>
		<form id="logs-filter" action="<?php echo esc_url( $this->url ); ?>" method="GET">
		<input type="hidden" name="page" value="mpsum-update-options" />
		<input type="hidden" name="tab" value="logs" />
		<div class="alignleft">
<?php
		if ( 'top' === $which && !is_singular() ) {

			$this->months_dropdown( $this->screen->post_type );
			$this->status_dropdown();
			$this->action_dropdown();
			$this->type_dropdown();

			submit_button( __( 'Filter', 'stops-core-theme-and-plugin-updates' ), 'button', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
		}
?>
		</div>
		</form><!-- #logs-filter -->
<?php
		/**
		 * Fires immediately following the closing "actions" div in the tablenav for the posts
		 * list table.
		 *
		 * @since 4.4.0
		 *
		 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
		 */
		do_action( 'manage_posts_extra_tablenav', $which );
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
