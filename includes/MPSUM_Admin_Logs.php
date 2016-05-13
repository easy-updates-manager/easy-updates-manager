<?php
/**
 * Controls the logs tab
 *
 * Controls the logs tab.
 *
 * @since 6.0.0
 *
 * @package WordPress
 */
class MPSUM_Admin_Logs {
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
	private $tab = 'logs';
	
	/**
	* Class constructor.
	*
	* Initialize the class
	*
	* @since 6.0.0
	* @access public
	*
	* @param string $slug Slug to the admin panel page
	*/
	public function __construct( $slug = '' ) {
		$this->slug = $slug;
		//Admin Tab Actions
		add_action( 'mpsum_admin_tab_logs', array( $this, 'tab_output_logs' ) );
	}
	
	/**
	* Output the HTML interface for the logs tab.
	*
	* Output the HTML interface for the logs tab.
	*
	* @since 6.0.0 
	* @access public
	* @see __construct
	* @internal Uses the mpsum_admin_tab_logs action
	*/
	public function tab_output_logs() {
		?>
        <h3><?php esc_html_e( 'Update Logs', 'stops-core-theme-and-plugin-updates' ); ?></h3>
        <p><?php esc_html_e( 'Please note that this feature does not necessarily work for premium themes and plugins.', 'stops-core-theme-and-plugin-updates' );?></p>
        <?php
	    $core_options = MPSUM_Updates_Manager::get_options( 'core' );
		$logs_table = new MPSUM_Logs_List_Table( $args = array( 'screen' => $this->slug, 'tab' => $this->tab ) );
		$logs_table->prepare_items();
		$logs_table->views();
		$logs_table->display();
		?>
    <?php
	} //end tab_output_plugins
}