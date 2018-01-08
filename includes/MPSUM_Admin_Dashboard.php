<?php
/**
 * Controls the main (general) tab
 *
 * Controls the main (general) tab and handles the saving of its options.
 *
 * @since 5.0.0
 *
 * @package WordPress
 */
class MPSUM_Admin_Dashboard {
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
	private $tab = 'main';

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
		add_action( 'mpsum_admin_tab_dashboard', array( $this, 'tab_output' ) );
    }


	/**
	* Output the HTML interface for the main tab.
	*
	* Output the HTML interface for the main tab.
	*
	* @since 5.0.0
	* @access public
	* @see __construct
	* @internal Uses the mpsum_admin_tab_main action
	*/
	public function tab_output() {
		?>
		<div class="eum-dashboard-app"></div>
        <?php
		return;
	} //end tab_output_plugins
}
