<?php
/**
 * Controls the advanced tab
 *
 * Controls the advanced tab and handles the saving of its options.
 *
 * @since 5.0.0
 *
 * @package WordPress
 */
class MPSUM_Admin_Advanced {
	/**
	* Holds the slug to the admin panel page
	*
	* @since 5.0.0
	* @access private
	* @var string $slug
	*/
	private $slug = '';
	
	/**
	* Holds the tab name
	*
	* @since 5.0.0
	* @access static
	* @var string $tab
	*/
	private $tab = 'advanced';
	
	/**
	* Class constructor.
	*
	* Initialize the class
	*
	* @since 5.0.0
	* @access public
	*
	* @param string $slug Slug to the admin panel page
	*/
	public function __construct( $slug = '' ) {
		$this->slug = $slug;
		//Admin Tab Actions
		add_action( 'mpsum_admin_tab_advanced', array( $this, 'tab_output' ) );	
		add_action( 'admin_init', array( $this, 'maybe_save_options' ) );
	}
		
	/**
	* Determine whether the save the advanced options or not.
	*
	* Determine whether the save the advanced options or not.
	*
	* @since 5.0.0 
	* @access public
	* @see __construct
	* @internal Uses admin_init action
	*
	*/
	public function maybe_save_options() {
		if ( !current_user_can( 'install_plugins' ) ) return;
		if ( !isset( $_GET[ 'page' ] ) || $_GET[ 'page' ] != $this->slug ) return;
		if ( !isset( $_POST[ 'action' ] ) ) return;
		if ( !isset( $_POST[ '_mpsum' ] ) ) return;
		
		//Get action
		$action = $_POST[ 'action' ];
		if ( empty( $action ) ) return;
		
		switch( $action ) {
			case 'mpsum_save_excluded_users':
				check_admin_referer( 'mpsum_exclude_users', '_mpsum' );
				$users = $_POST[ 'mpsum_excluded_users' ];
				if ( !is_array( $users ) || empty( $users ) ) return;
				$users_to_save = array();
				foreach( $users as $index => $user_id ) {
					$user_id = absint( $user_id );
					if ( 0 === $user_id ) continue;
					$users_to_save[] = $user_id;
				}
				MPSUM_Updates_Manager::update_options( $users_to_save, 'excluded_users' );
				break;
			case 'mpsum_reset_options':
				check_admin_referer( 'mpsum_reset_options', '_mpsum' );
				MPSUM_Updates_Manager::update_options( array() );
				break;
            case 'mpsum_force_updates':
                wp_schedule_single_event( time() + 10, 'wp_update_plugins' );
                wp_schedule_single_event( time() + 10, 'wp_version_check' );
                wp_schedule_single_event( time() + 10, 'wp_update_themes' );
                wp_schedule_single_event( time() + 45, 'wp_maybe_auto_update' );
                break;
			default:
				return;	
		}
		
		//Redirect args
		$query_args = array();
		$query_args[ 'updated' ] = "1";
		$query_args[ 'tab' ] = $this->tab;
		$query_args[ 'mpaction' ] = $action;
		
				
		//Redirect back to settings screen
		wp_redirect( esc_url_raw( add_query_arg( $query_args, MPSUM_Admin::get_url() ) ) );
		exit;
	}
	
	/**
	* Output the HTML interface for the advanced tab.
	*
	* Output the HTML interface for the advanced tab.
	*
	* @since 5.0.0 
	* @access public
	* @see __construct
	* @internal Uses the mpsum_admin_tab_main action
	*/
	public function tab_output() {
    	
		if ( isset( $_GET[ 'updated' ] ) ) {    
    		$action = isset( $_GET[ 'mpaction' ] ) ? $_GET[ 'mpaction' ] : '';		
            switch( $action ) {
                case 'mpsum_save_excluded_users':
                    $message = __( 'The exclusion of users option has been updated.', 'stops-core-theme-and-plugin-updates' );
                    break;
                case 'mpsum_reset_options':
                    $message = __( 'The plugin settings have now been reset.', 'stops-core-theme-and-plugin-updates' );
                    break;
                case 'mpsum_force_updates':
                    $message = __( 'Force update checks have been initialized. Please check your site in 90 seconds, and refresh to test automatic updates.', 'stops-core-theme-and-plugin-updates' );
                    break;
                default:
                    $message = __( 'Options saved.', 'stops-core-theme-and-plugin-updates' );
                	return;	
            }
    		
			?>
			<br />
			<div class="updated"><p><strong><?php echo esc_html( $message ); ?></strong></p></div>
			<?php
		}
		
		?>
        <form action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="post">
		<h3><?php esc_html_e( 'Exclude Users', 'stops-core-theme-and-plugin-updates' ); ?></h3>
		<p><?php esc_html_e( 'Select which users to be excluded from the settings of this plugin.  Default WordPress behavior will be used.', 'stops-core-theme-and-plugin-updates' ); ?></p>
		<p><?php esc_html_e( 'This option is useful if, for example, you would like to disable updates, but have a user account that can still update WordPress.', 'stops-core-theme-and-plugin-updates' ); ?></p>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Users to be Excluded', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<?php
						//Code from wp-admin/includes/class-wp-ms-users-list-table
						$users = array();
						if ( is_multisite() ) {
							global $wpdb;
							$logins = implode( "', '", get_super_admins() );
							$users = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE user_login IN ('$logins')" );
						
						} else {
							/**
							* Determine which role gets queried for admin users.
							*
							* Determine which role gets queried for admin users.
							*
							* @since 5.0.0
							*
							* @param string  $var administrator.
							*/
							$role = apply_filters( 'mpsum_admin_role', 'administrator' );
							$users = get_users( array( 'role' => $role, 'orderby' => 'display_name', 'order' => 'ASC', 'fields' => 'ID' ) );	
						}
						if ( is_array( $users ) && !empty( $users ) ) {
							echo '<input type="hidden" value="0" name="mpsum_excluded_users[]" />';
							$excluded_users = MPSUM_Updates_Manager::get_options( 'excluded_users' );
							foreach( $users as $index => $user_id ) {
								$user = get_userdata( $user_id );
								printf( '<input type="checkbox" name="mpsum_excluded_users[]" id="mpsum_user_%1$d" value="%1$d" %3$s />&nbsp;<label for="mpsum_user_%1$d">%2$s</label><br />', esc_attr( $user_id ), esc_html( $user->display_name ), checked( true, in_array( $user_id, $excluded_users ), false ) );
							}
						}
						?>
				</td>
			</tr>
		</table>
		<input type="hidden" name="action" value='mpsum_save_excluded_users' />
	    <?php
		wp_nonce_field( 'mpsum_exclude_users', '_mpsum' );
		echo '<p class="submit">';
		submit_button( __( 'Save Users', 'stops-core-theme-and-plugin-updates' ) , 'primary', 'submit', false );
		echo '</p>';
		?>
        </form>
         <form action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="post">
		<h3><?php esc_html_e( 'Reset Options', 'stops-core-theme-and-plugin-updates' ); ?></h3>
		<p><?php esc_html_e( 'This will reset all options to as if you have just installed the plugin.', 'stops-core-theme-and-plugin-updates' ); ?></p>
		<input type="hidden" name="action" value='mpsum_reset_options' />
	    <?php
		wp_nonce_field( 'mpsum_reset_options', '_mpsum' );
		echo '<p class="submit">';
		submit_button( __( 'Reset All Options', 'stops-core-theme-and-plugin-updates' ) , 'primary', 'submit', false );
		echo '</p>';
		?>
        </form>
        <form action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="post">
		<h3><?php esc_html_e( 'Force Automatic Updates', 'stops-core-theme-and-plugin-updates' ); ?></h3>
		<p><?php esc_html_e( 'This will attempt to force automatic updates. This is useful for debugging.', 'stops-core-theme-and-plugin-updates' ); ?></p>
		<input type="hidden" name="action" value='mpsum_force_updates' />
	    <?php
		wp_nonce_field( 'mpsum_force_updates', '_mpsum' );
		echo '<p class="submit">';
		submit_button( __( 'Force Updates', 'stops-core-theme-and-plugin-updates' ) , 'primary', 'submit', false );
		echo '</p>';
		?>
        </form>
    <?php
	} //end tab_output
}