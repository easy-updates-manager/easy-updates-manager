<?php
/**
 * Easy Updates Manager Plugins List Table class.
 *
 * @package WordPress
 * @subpackage MPSUM_List_Table
 * @since 5.0.0
 * @access private
 */
class MPSUM_Plugins_List_Table extends MPSUM_List_Table {
	
	private $tab = '';

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		global $status, $page;

		parent::__construct( array(
			'plural' => 'plugins',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );
		
		$this->tab = isset( $args[ 'tab' ] ) ? $args[ 'tab' ] : '';
		

		$status = 'all';
		if ( isset( $_REQUEST['plugin_status'] ) && in_array( $_REQUEST['plugin_status'], array( 'update_disabled', 'update_enabled', 'automatic' ) ) ) {
			$status = $_REQUEST['plugin_status'];
		}

		if ( isset($_REQUEST['s']) )
			$_SERVER['REQUEST_URI'] = add_query_arg('s', wp_unslash($_REQUEST['s']) );

		$page = $this->get_pagenum();
	}

	protected function get_table_classes() {
		return array( 'widefat', $this->_args['plural'] );
	}

	public function ajax_user_can() {
		return current_user_can('activate_plugins');
	}

	public function prepare_items() {
		
		global $orderby, $order, $totals, $status;
		$order = 'DESC';
		$page = isset( $_GET[ 'paged' ] ) ? absint( $_GET[ 'paged' ] ) : 1;
		$orderby = 'Name';

		/**
		 * Filter the full array of plugins to list in the Plugins list table.
		 *
		 * @since 3.0.0
		 *
		 * @see get_plugins()
		 *
		 * @param array $plugins An array of plugins to display in the list table.
		 */
		$plugins = array(
			'all' => apply_filters( 'all_plugins', get_plugins() ),
			'update_enabled' => array(),
			'update_disabled' => array(),
			'automatic' => array()
		);

		$screen = $this->screen;

		
		$plugin_info = get_site_transient( 'update_plugins' );
		
		$plugin_options = MPSUM_Updates_Manager::get_options( 'plugins' );
		$plugin_automatic_options = MPSUM_Updates_Manager::get_options( 'plugins_automatic' );
		foreach ( (array) $plugins['all'] as $plugin_file => $plugin_data ) {
			// Extra info if known. array_merge() ensures $plugin_data has precedence if keys collide.
			if ( isset( $plugin_info->response[ $plugin_file ] ) ) {
				$plugins['all'][ $plugin_file ] = $plugin_data = array_merge( (array) $plugin_info->response[ $plugin_file ], $plugin_data );
			} elseif ( isset( $plugin_info->no_update[ $plugin_file ] ) ) {
				$plugins['all'][ $plugin_file ] = $plugin_data = array_merge( (array) $plugin_info->no_update[ $plugin_file ], $plugin_data );
			}
			
			
			if ( false !== $key = array_search( $plugin_file, $plugin_options ) ) {
				$plugins[ 'update_disabled' ][ $plugin_file ] = $plugin_data;
			} else {
				$plugins[ 'update_enabled' ][ $plugin_file ] = $plugin_data;
				if ( in_array( $plugin_file, $plugin_automatic_options ) ) {
					$plugins[ 'automatic' ][ $plugin_file ] = $plugin_data;
				}
			}
		}
		
		$totals = array();
		foreach ( $plugins as $type => $list )
			$totals[ $type ] = count( $list );
			
		//Disable the automatic updates view
		$core_options = MPSUM_Updates_Manager::get_options( 'core' );
		if ( isset( $core_options[ 'automatic_plugin_updates' ] ) && 'individual' !== $core_options[ 'automatic_plugin_updates' ] ) {
			unset( $totals[ 'automatic' ] );
			$plugins[ 'automatic' ] = array();
		}
		
		if ( empty( $plugins[ $status ] ) )
			$status = 'all';

		$this->items = array();
		foreach ( $plugins[ $status ] as $plugin_file => $plugin_data ) {
			// Translate, Don't Apply Markup, Sanitize HTML
			remove_action( "after_plugin_row_$plugin_file", 'wp_plugin_update_row', 10, 2 );
			$this->items[$plugin_file] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
		}

		$total_this_page = $totals[ $status ];

		$plugins_per_page = 999;
		
		$start = ( $page - 1 ) * $plugins_per_page;

		if ( $total_this_page > $plugins_per_page )
			$this->items = array_slice( $this->items, $start, $plugins_per_page );
		
		$this->set_pagination_args( array(
			'total_items' => $total_this_page,
			'per_page' => $plugins_per_page,
		) );
	}

	/**
	 * @staticvar string $term
	 * @param array $plugin
	 * @return boolean
	 */
	public function _search_callback( $plugin ) {
		static $term;
		if ( is_null( $term ) )
			$term = wp_unslash( $_REQUEST['s'] );

		foreach ( $plugin as $value ) {
			if ( false !== stripos( strip_tags( $value ), $term ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @global string $orderby
	 * @global string $order
	 * @param array $plugin_a
	 * @param array $plugin_b
	 * @return int
	 */
	public function _order_callback( $plugin_a, $plugin_b ) {
		global $orderby, $order;

		$a = $plugin_a[$orderby];
		$b = $plugin_b[$orderby];

		if ( $a == $b )
			return 0;

		if ( 'DESC' == $order )
			return ( $a < $b ) ? 1 : -1;
		else
			return ( $a < $b ) ? -1 : 1;
	}

	public function no_items() {
		global $plugins;

		if ( !empty( $plugins['all'] ) )
			_e( 'No plugins found.', 'stops-core-theme-and-plugin-updates' );
		else
			_e( 'You do not appear to have any plugins available at this time.', 'stops-core-theme-and-plugin-updates' );
	}

	public function get_columns() {
		global $status;

		return array(
			'cb'          => !in_array( $status, array( 'mustuse', 'dropins' ) ) ? '<input type="checkbox" />' : '',
			'name'        => __( 'Plugin', 'stops-core-theme-and-plugin-updates' ),
			'description' => __( 'Description', 'stops-core-theme-and-plugin-updates' ),
		);
	}

	protected function get_sortable_columns() {
		return array();
	}

	protected function get_views() {
		global $totals, $status;

		$status_links = array();
		foreach ( $totals as $type => $count ) {
			if ( !$count )
				continue;

			switch ( $type ) {
				case 'all':
					$text = _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $count, 'plugins' );
					break;
				case 'update_enabled':
					$text = _n( 'Updates Enabled <span class="count">(%s)</span>', 'Updates Enabled <span class="count">(%s)</span>', $count, 'stops-core-theme-and-plugin-updates' );
					break;
				case 'update_disabled':
					$text = _n( 'Updates Disabled <span class="count">(%s)</span>', 'Updates Disabled <span class="count">(%s)</span>', $count, 'stops-core-theme-and-plugin-updates' );
					break;
				case 'automatic':
					$text = _n( 'Automatic Updates <span class="count">(%s)</span>', 'Automatic Updates <span class="count">(%s)</span>', $count, 'stops-core-theme-and-plugin-updates' );
					break;
			}

			if ( 'search' != $type ) {
				$plugin_url = MPSUM_Admin::get_url();
				$query_args = array(
					'tab' => $this->tab,
					'plugin_status' => $type
				);
				$status_links[$type] = sprintf( "<a href='%s' %s>%s</a>",
					add_query_arg( $query_args, $plugin_url ),
					( $type == $status ) ? ' class="current"' : '',
					sprintf( $text, number_format_i18n( $count ) )
					);
			}
		}

		return $status_links;
	}

	protected function get_bulk_actions() {
		global $status;

		$actions = array();
		        
		$actions[ 'allow-update-selected' ] = esc_html__( 'Allow Updates', 'stops-core-theme-and-plugin-updates' );
		$actions[ 'disallow-update-selected' ] = esc_html__( 'Disallow Updates', 'stops-core-theme-and-plugin-updates' );
		$core_options = MPSUM_Updates_Manager::get_options( 'core' );
		if ( isset( $core_options[ 'automatic_plugin_updates' ] ) && 'individual' == $core_options[ 'automatic_plugin_updates' ] ) {
			$actions[ 'allow-automatic-selected' ] = esc_html__( 'Allow Automatic Updates', 'stops-core-theme-and-plugin-updates' );
			$actions[ 'disallow-automatic-selected' ] = esc_html__( 'Disallow Automatic Updates', 'stops-core-theme-and-plugin-updates' );
		}
		
		return $actions;
	}

	/**
	 * @global string $status
	 * @param string $which
	 * @return null
	 */
	public function bulk_actions( $which = '' ) {
		global $status;

		if ( in_array( $status, array( 'mustuse', 'dropins' ) ) )
			return;

		parent::bulk_actions( $which );
	}

	/**
	 * @global string $status
	 * @param string $which
	 * @return null
	 */
	protected function extra_tablenav( $which ) {
		global $status;

		if ( ! in_array($status, array('recently_activated', 'mustuse', 'dropins') ) )
			return;

		echo '<div class="alignleft actions">';

		if ( ! $this->screen->in_admin( 'network' ) && 'recently_activated' == $status ) {
			submit_button( __( 'Clear List', 'stops-core-theme-and-plugin-updates' ), 'button', 'clear-recent-list', false );
		}
		elseif ( 'top' == $which && 'mustuse' == $status ) {
			echo '<p>' .
				sprintf(
					__( 'Files in the %s directory are executed automatically.', 'stops-core-theme-and-plugin-updates' ),
					'<code>' . str_replace( ABSPATH, '/', WPMU_PLUGIN_DIR ) . '</code>'
				) .
				'</p>';
		}
		elseif ( 'top' == $which && 'dropins' == $status ) {
			echo '<p>' .
				sprintf(
					__( 'Drop-ins are advanced plugins in the %s directory that replace WordPress functionality when present.', 'stops-core-theme-and-plugin-updates' ),
					'<code>' . str_replace( ABSPATH, '', WP_CONTENT_DIR ) . '</code>'
				) .
				'</p>';
		}

		echo '</div>';
	}

	public function current_action() {
		if ( isset($_POST['clear-recent-list']) )
			return 'clear-recent-list';

		return parent::current_action();
	}

	public function display_rows() {
		global $status;

		if ( is_multisite() && ! $this->screen->in_admin( 'network' ) && in_array( $status, array( 'mustuse', 'dropins' ) ) )
			return;

		foreach ( $this->items as $plugin_file => $plugin_data )
			$this->single_row( array( $plugin_file, $plugin_data ) );
	}

	/**
	 * @global string $status
	 * @global int $page
	 * @global string $s
	 * @global array $totals
	 * @param array $item
	 */
	public function single_row( $item ) {
		global $status, $page, $s, $totals;

		list( $plugin_file, $plugin_data ) = $item;
		$context = 'all';
		$screen = $this->screen;
		
		/**
		* Filter the action links that show up under each plugin row.
		*
		* @since 5.0.0
		*
		* @param string    Relative plugin file path
		* @param array  $plugin_data An array of plugin data.
		* @param string   $status     Status of the plugin.
		*/
		$actions = apply_filters( 'mpsum_plugin_action_links', array(), $plugin_file, $plugin_data, $status );

		$class = 'active';
		$plugin_options = MPSUM_Updates_Manager::get_options( 'plugins' );
		if ( false !== $key = array_search( $plugin_file, $plugin_options ) ) {
			$class = 'inactive';
		} 
		$checkbox_id =  "checkbox_" . md5($plugin_data['Name']);
		$checkbox = "<label class='screen-reader-text' for='" . $checkbox_id . "' >" . sprintf( __( 'Select %s', 'stops-core-theme-and-plugin-updates' ), $plugin_data['Name'] ) . "</label>"
				. "<input type='checkbox' name='checked[]' value='" . esc_attr( $plugin_file ) . "' id='" . $checkbox_id . "' />";
		$description = '<p>' . ( $plugin_data['Description'] ? $plugin_data['Description'] : '&nbsp;' ) . '</p>';
		$plugin_name = $plugin_data['Name'];

		$id = sanitize_title( $plugin_name );

		echo "<tr id='$id' class='$class'>";

		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			switch ( $column_name ) {
				case 'cb':
					echo "<th scope='row' class='check-column'>$checkbox</th>";
					break;
				case 'name':
					echo "<td class='plugin-title'$style><strong>$plugin_name</strong>";
					echo $this->row_actions( $actions, true );
					echo "</td>";
					break;
				case 'description':
					echo "<td class='column-description desc'$style>
						<div class='plugin-description'>$description</div>
						<div class='$class second plugin-version-author-uri'>";

					$plugin_meta = array();
					if ( !empty( $plugin_data['Version'] ) )
						$plugin_meta[] = sprintf( __( 'Version %s', 'stops-core-theme-and-plugin-updates' ), $plugin_data['Version'] );
					if ( !empty( $plugin_data['Author'] ) ) {
						$author = $plugin_data['Author'];
						if ( !empty( $plugin_data['AuthorURI'] ) )
							$author = '<a href="' . $plugin_data['AuthorURI'] . '">' . $plugin_data['Author'] . '</a>';
						$plugin_meta[] = sprintf( __( 'By %s', 'stops-core-theme-and-plugin-updates' ), $author );
					}

					// Details link using API info, if available
					if ( isset( $plugin_data['slug'] ) && current_user_can( 'install_plugins' ) ) {
						$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
							esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_data['slug'] .
								'&TB_iframe=true&width=600&height=550' ) ),
							esc_attr( sprintf( __( 'More information about %s', 'stops-core-theme-and-plugin-updates' ), $plugin_name ) ),
							esc_attr( $plugin_name ),
							__( 'View details', 'stops-core-theme-and-plugin-updates' )
						);
					} elseif ( ! empty( $plugin_data['PluginURI'] ) ) {
						$plugin_meta[] = sprintf( '<a href="%s">%s</a>',
							esc_url( $plugin_data['PluginURI'] ),
							__( 'Visit plugin site', 'stops-core-theme-and-plugin-updates' )
						);
					}

					/**
					 * Filter the array of row meta for each plugin in the Plugins list table.
					 *
					 * @since 2.8.0
					 *
					 * @param array  $plugin_meta An array of the plugin's metadata,
					 *                            including the version, author,
					 *                            author URI, and plugin URI.
					 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
					 * @param array  $plugin_data An array of plugin data.
					 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
					 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
					 *                            'Drop-ins', 'Search'.
					 */
					$plugin_meta = apply_filters( 'plugin_row_meta', $plugin_meta, $plugin_file, $plugin_data, $status );
					echo implode( ' | ', $plugin_meta );

					echo "</div></td>";
					break;
				default:
					echo "<td class='$column_name column-$column_name'$style>";

					/**
					 * Fires inside each custom column of the Plugins list table.
					 *
					 * @since 3.1.0
					 *
					 * @param string $column_name Name of the column.
					 * @param string $plugin_file Path to the plugin file.
					 * @param array  $plugin_data An array of plugin data.
					 */
					do_action( 'manage_plugins_custom_column', $column_name, $plugin_file, $plugin_data );
					echo "</td>";
			}
		}

		echo "</tr>";

		/**
		 * Fires after each row in the Plugins list table.
		 *
		 * @since 2.3.0
		 *
		 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
		 * @param array  $plugin_data An array of plugin data.
		 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
		 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                            'Drop-ins', 'Search'.
		 */
		do_action( 'after_plugin_row', $plugin_file, $plugin_data, $status );

		/**
		 * Fires after each specific row in the Plugins list table.
		 *
		 * The dynamic portion of the hook name, `$plugin_file`, refers to the path
		 * to the plugin file, relative to the plugins directory.
		 *
		 * @since 2.7.0
		 *
		 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
		 * @param array  $plugin_data An array of plugin data.
		 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
		 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                            'Drop-ins', 'Search'.
		 */
		do_action( "after_plugin_row_$plugin_file", $plugin_file, $plugin_data, $status );
	}
}
