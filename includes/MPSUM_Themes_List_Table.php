<?php
/**
 * Easy Updates Manager Themes List Table class.
 *
 * @package Easy Updates Manager
 * @subpackage MPSUM_List_Table
 * @since 5.0.0
 * @access private
 */
class MPSUM_Themes_List_Table extends MPSUM_List_Table {

	public $site_id;
	public $is_site_themes;
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
			'plural' => 'themes',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );
		
		$this->tab = isset( $args[ 'tab' ] ) ? $args[ 'tab' ] : '';

		$status = isset( $_REQUEST['theme_status'] ) ? $_REQUEST['theme_status'] : 'all';
		if ( !in_array( $status, array( 'all', 'update_disabled', 'update_enabled', 'automatic' ) ) )
			$status = 'all';

		$page = $this->get_pagenum();

		$this->is_site_themes = ( 'site-themes-network' == $this->screen->id ) ? true : false;

		if ( $this->is_site_themes )
			$this->site_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
	}

	protected function get_table_classes() {
		// todo: remove and add CSS for .themes
		return array( 'widefat', 'plugins' );
	}

	public function ajax_user_can() {
		if ( $this->is_site_themes )
			return current_user_can( 'manage_sites' );
		else
			return current_user_can( 'manage_network_themes' );
	}

	public function prepare_items() {		
		global $totals, $status;
		$order = 'DESC';
		$page = isset( $_GET[ 'paged' ] ) ? absint( $_GET[ 'paged' ] ) : 1;
		$orderby = 'Name';
		
		$themes = array(
			/**
			 * Filter the full array of WP_Theme objects to list in the Multisite
			 * themes list table.
			 *
			 * @since 3.1.0
			 *
			 * @param array $all An array of WP_Theme objects to display in the list table.
			 */
			'all' => apply_filters( 'all_themes', wp_get_themes() ),
			'update_enabled' => array(),
			'update_disabled' => array(),
			'automatic' => array()
			
		);


		$maybe_update = current_user_can( 'update_themes' ) && ! $this->is_site_themes && $current = get_site_transient( 'update_themes' );
		$theme_options = MPSUM_Updates_Manager::get_options( 'themes' );
		$theme_automatic_options = MPSUM_Updates_Manager::get_options( 'themes_automatic' );
		foreach ( (array) $themes['all'] as $theme => $theme_data ) {
			if ( false !== $key = array_search( $theme, $theme_options ) ) {
				$themes[ 'update_disabled' ][ $theme ] = $theme_data;
			} else {
				$themes[ 'update_enabled' ][ $theme ] = $theme_data;
				if ( in_array( $theme, $theme_automatic_options ) ) {
					$themes[ 'automatic' ][ $theme ] = $theme_data;
				}
			}
		}		
		
		$totals = array();
		
		foreach ( $themes as $type => $list ) 
			$totals[ $type ] = count( $list );
			
		//Disable the automatic updates view
		$core_options = MPSUM_Updates_Manager::get_options( 'core' );
		if ( isset( $core_options[ 'automatic_theme_updates' ] ) && 'individual' !== $core_options[ 'automatic_theme_updates' ] ) {
			unset( $totals[ 'automatic' ] );
			$themes[ 'automatic' ] = array();
		}
			
		if ( empty( $themes[ $status ] ) )
			$status = 'all';
			
		$this->items = $themes[ $status ];
		WP_Theme::sort_by_name( $this->items );

		$this->has_items = ! empty( $themes['all'] );
		$total_this_page = $totals[ $status ];

		if ( $orderby ) {
			$orderby = ucfirst( $orderby );
			$order = strtoupper( $order );

			if ( $orderby == 'Name' ) {
				if ( 'ASC' == $order )
					$this->items = array_reverse( $this->items );
			} else {
				uasort( $this->items, array( $this, '_order_callback' ) );
			}
		}
		$total_this_page = count( $themes[ 'all' ] );
		
		// Get themes per page
		$user_id = get_current_user_id();
		$themes_per_page = get_user_meta( $user_id, 'mpsum_items_per_page', true );
		if ( ! is_numeric( $themes_per_page ) ) {
			$themes_per_page = 100;
		}
		
		$start = ( $page - 1 ) * $themes_per_page;

		if ( $total_this_page > $themes_per_page )
			$this->items = array_slice( $this->items, $start, $themes_per_page, true );

		$this->set_pagination_args( array(
			'total_items' => $total_this_page,
			'per_page' => $themes_per_page,
		) );
	}

	/**
	 * @staticvar string $term
	 * @param WP_Theme $theme
	 * @return bool
	 */
	public function _search_callback( $theme ) {
		static $term;
		if ( is_null( $term ) )
			$term = wp_unslash( $_REQUEST['s'] );

		foreach ( array( 'Name', 'Description', 'Author', 'Author', 'AuthorURI' ) as $field ) {
			// Don't mark up; Do translate.
			if ( false !== stripos( $theme->display( $field, false, true ), $term ) )
				return true;
		}

		if ( false !== stripos( $theme->get_stylesheet(), $term ) )
			return true;

		if ( false !== stripos( $theme->get_template(), $term ) )
			return true;

		return false;
	}

	// Not used by any core columns.
	/**
	 * @global string $orderby
	 * @global string $order
	 * @param array $theme_a
	 * @param array $theme_b
	 * @return int
	 */
	public function _order_callback( $theme_a, $theme_b ) {
		global $orderby, $order;

		$a = $theme_a[ $orderby ];
		$b = $theme_b[ $orderby ];

		if ( $a == $b )
			return 0;

		if ( 'DESC' == $order )
			return ( $a < $b ) ? 1 : -1;
		else
			return ( $a < $b ) ? -1 : 1;
	}

	public function no_items() {
		if ( ! $this->has_items )
			_e( 'No themes found.', 'stops-core-theme-and-plugin-updates'  );
		else
			_e( 'You do not appear to have any themes available at this time.', 'stops-core-theme-and-plugin-updates'  );
	}

	public function get_columns() {
		global $status;

		return array(
			'cb'          => '<input type="checkbox" />',
			'name'        => __( 'Theme', 'stops-core-theme-and-plugin-updates' ),
			'description' => __( 'Description', 'stops-core-theme-and-plugin-updates'  ),
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
					$text = _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $count, 'themes' );
					break;
				case 'update_disabled':
					$text = _n( 'Updates Disabled <span class="count">(%s)</span>', 'Updates Disabled <span class="count">(%s)</span>', $count, 'stops-core-theme-and-plugin-updates' );
					break;
				case 'update_enabled':
					$text = _n( 'Updates Enabled <span class="count">(%s)</span>', 'Updates Enabled <span class="count">(%s)</span>', $count, 'stops-core-theme-and-plugin-updates' );
					break;
				case 'automatic':
					$text = _n( 'Automatic Updates <span class="count">(%s)</span>', 'Automatic Updates <span class="count">(%s)</span>', $count, 'stops-core-theme-and-plugin-updates' );
					break;
			}

			if ( 'search' != $type ) {
				$theme_url = MPSUM_Admin::get_url();
				$query_args = array(
					'tab' => $this->tab,
					'theme_status' => $type
				);

				$status_links[$type] = sprintf( "<a href='%s' %s>%s</a>",
					add_query_arg( $query_args, $theme_url ),
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
		if ( isset( $core_options[ 'automatic_theme_updates' ] ) && 'individual' == $core_options[ 'automatic_theme_updates' ] ) {
			$actions[ 'allow-automatic-selected' ] = esc_html__( 'Allow Automatic Updates', 'stops-core-theme-and-plugin-updates' );
			$actions[ 'disallow-automatic-selected' ] = esc_html__( 'Disallow Automatic Updates', 'stops-core-theme-and-plugin-updates' );
		}
		
		return $actions;
	}

	public function display_rows() {
		foreach ( $this->items as $theme )
			$this->single_row( $theme );
	}

	/**
	 * @global string $status
	 * @global int $page
	 * @global string $s
	 * @global array $totals
	 * @param WP_Theme $theme
	 */
	public function single_row( $theme ) {
		$status = 'all';
		$stylesheet = $theme->get_stylesheet();
		remove_action( "after_theme_row_$stylesheet", 'wp_theme_update_row', 10, 2 );
		$theme_key = urlencode( $stylesheet );

		/**
		* Filter the action links that show up under each theme row.
		*
		* @since 5.0.0
		*
		* @param array    Array of action links
		* @param WP_Theme   $theme WP_Theme object
		* @param string   $status     Status of the theme.
		*/
		$actions = apply_filters( 'mpsum_theme_action_links', array(), $theme, 'all' );

		$checkbox_id = "checkbox_" . md5( $theme->get('Name') );
		$checkbox = "<input type='checkbox' name='checked[]' value='" . esc_attr( $stylesheet ) . "' id='" . $checkbox_id . "' /><label class='screen-reader-text' for='" . $checkbox_id . "' >" . __('Select', 'stops-core-theme-and-plugin-updates' ) . " " . $theme->display('Name') . "</label>";

		$id = sanitize_html_class( $theme->get_stylesheet() );
		$class = 'active';
		$theme_options = MPSUM_Updates_Manager::get_options( 'themes' );
		if ( false !== $key = array_search( $stylesheet, $theme_options ) ) {
			$class = 'inactive';
		} 
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
					echo "<td class='theme-title'$style><strong>" . $theme->display('Name') . "</strong>";
					echo $this->row_actions( $actions, true );
					echo "</td>";
					break;
				case 'description':
					echo "<td class='column-description desc'$style>";
					if ( $theme->errors() ) {
						$pre = $status == 'broken' ? __( 'Broken Theme:', 'stops-core-theme-and-plugin-updates'  ) . ' ' : '';
						echo '<p><strong class="attention">' . $pre . $theme->errors()->get_error_message() . '</strong></p>';
					}
					echo "<div class='theme-description'><p>" . $theme->display( 'Description' ) . "</p></div>
						<div class='second theme-version-author-uri'>";

					$theme_meta = array();

					if ( $theme->get('Version') )
						$theme_meta[] = sprintf( __( 'Version %s', 'stops-core-theme-and-plugin-updates'  ), $theme->display('Version') );

					$theme_meta[] = sprintf( __( 'By %s', 'stops-core-theme-and-plugin-updates' ), $theme->display('Author') );

					if ( $theme->get('ThemeURI') )
						$theme_meta[] = '<a href="' . $theme->display('ThemeURI') . '" title="' . esc_attr__( 'Visit theme homepage', 'stops-core-theme-and-plugin-updates' ) . '">' . __( 'Visit Theme Site', 'stops-core-theme-and-plugin-updates' ) . '</a>';

					/**
					 * Filter the array of row meta for each theme in the Multisite themes
					 * list table.
					 *
					 * @since 3.1.0
					 *
					 * @param array    $theme_meta An array of the theme's metadata,
					 *                             including the version, author, and
					 *                             theme URI.
					 * @param string   $stylesheet Directory name of the theme.
					 * @param WP_Theme $theme      WP_Theme object.
					 * @param string   $status     Status of the theme.
					 */
					$theme_meta = apply_filters( 'theme_row_meta', $theme_meta, $stylesheet, $theme, $status );
					echo implode( ' | ', $theme_meta );

					echo "</div></td>";
					break;

				default:
					echo "<td class='$column_name column-$column_name'$style>";

					/**
					 * Fires inside each custom column of the Multisite themes list table.
					 *
					 * @since 3.1.0
					 *
					 * @param string   $column_name Name of the column.
					 * @param string   $stylesheet  Directory name of the theme.
					 * @param WP_Theme $theme       Current WP_Theme object.
					 */
					do_action( 'manage_themes_custom_column', $column_name, $stylesheet, $theme );
					echo "</td>";
			}
		}

		echo "</tr>";

		if ( $this->is_site_themes )
			remove_action( "after_theme_row_$stylesheet", 'wp_theme_update_row' );

		/**
		 * Fires after each row in the Multisite themes list table.
		 *
		 * @since 3.1.0
		 *
		 * @param string   $stylesheet Directory name of the theme.
		 * @param WP_Theme $theme      Current WP_Theme object.
		 * @param string   $status     Status of the theme.
		 */
		do_action( 'after_theme_row', $stylesheet, $theme, $status );

		/**
		 * Fires after each specific row in the Multisite themes list table.
		 *
		 * The dynamic portion of the hook name, `$stylesheet`, refers to the
		 * directory name of the theme, most often synonymous with the template
		 * name of the theme.
		 *
		 * @since 3.5.0
		 *
		 * @param string   $stylesheet Directory name of the theme.
		 * @param WP_Theme $theme      Current WP_Theme object.
		 * @param string   $status     Status of the theme.
		 */
		do_action( "after_theme_row_$stylesheet", $stylesheet, $theme, $status );
	}
}
