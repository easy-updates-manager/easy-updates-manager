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
	* Get tab defaults.
	*
	* Get default core plugin options.
	*
	* @since 5.0.0
	* @access private
	*
	* @return array Associative array of default options
	*/
	private function get_defaults() {
		return array(
			'all_updates' => 'on',
			'core_updates' => 'on',
			'plugin_updates' => 'on',
			'theme_updates' => 'on',
			'translation_updates' => 'on',
			'automatic_development_updates' => 'off',
			'automatic_major_updates' => 'off',
			'automatic_minor_updates' => 'on',
			'automatic_plugin_updates' => 'default',
			'automatic_theme_updates' => 'default',
			'automatic_translation_updates' => 'on',
			'notification_core_update_emails' => 'on',
			'misc_browser_nag' => 'on',
			'misc_wp_footer' => 'on',
			'notification_core_update_emails_plugins' => 'on',
			'notification_core_update_emails_themes' => 'on',
			'notification_core_update_emails_translations' => 'on' 
		);	
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
		$options = MPSUM_Updates_Manager::get_options( 'core' );
		$options = wp_parse_args( $options, $this->get_defaults() );
		echo '<pre>' . print_r( $options, true ) . '</pre>';
		?>
		<div id="dashboard-main-outputs">
    		<div class="dashboard-main-wrapper">
        		<div class="dashboard-main-header">WordPress Updates</div><!-- .dashboard-main-header -->
        		<div class="dashboard-item-wrapper">
            		<div class="dashboard-item" "dashboard-main">
                		<div class="dashboard-item-header"><?php esc_html_e( 'All Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                		</div><!-- .dashboard-item-header -->
                		<div class="dashboard-item-choice">
                    		<?php
                            $disable_core_options = false;
                            if( 'off' == $options[ 'all_updates' ] ) {
                                $disable_core_options = true;
                                $options[ 'core_updates' ] = 'off'; 
                                $options[ 'plugin_updates' ] = 'off';
                                $options[ 'theme_updates' ] = 'off';
                                $options[ 'translation_updates' ] = 'off'; 
                            }
                            ?>
                            <input type="checkbox" name="options[all_updates]" value="on"  />
                            <input type="checkbox"  class="dashboard-hide" name="options[all_updates]" value="off" id="all_updates_on" <?php checked( 'on', $options[ 'all_updates' ] ); ?> />&nbsp;<label for="all_updates_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		</div><!-- .dashboard-item-choice -->
            		</div><!-- dashboard-item-->
            		<div class="dashboard-item" "dashboard-main">
                		<div class="dashboard-item-header"><?php esc_html_e( 'WordPress Core Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                		</div><!-- .dashboard-item-header -->
                		<div class="dashboard-item-choice">
                    		<input type="hidden" name="options[core_updates]" value="on" />
            				<input type="checkbox"  class="dashboard-hide" name="options[core_updates]" value="off" id="core_updates_off" <?php checked( 'on', $options[ 'core_updates' ] ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="core_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		</div><!-- .dashboard-item-choice -->
            		</div><!-- dashboard-item-->
            		<div class="dashboard-item" "dashboard-main">
                		<div class="dashboard-item-header"><?php esc_html_e( 'All Plugin Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                		</div><!-- .dashboard-item-header -->
                		<div class="dashboard-item-choice">
                    		<input type="hidden" name="options[plugin_updates]" value="on" /> 
            				<input type="checkbox" class="dashboard-hide"  name="options[plugin_updates]" value="off" id="plugin_updates_off" <?php checked( 'on', $options[ 'plugin_updates' ] ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="plugin_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		</div><!-- .dashboard-item-choice -->
            		</div><!-- dashboard-item-->
            		<div class="dashboard-item" "dashboard-main">
                		<div class="dashboard-item-header"><?php esc_html_e( 'All Theme Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                		</div><!-- .dashboard-item-header -->
                		<div class="dashboard-item-choice">
                    		<input type="hidden" name="options[theme_updates]" value="on" />
            				<input type="checkbox"  class="dashboard-hide" name="options[theme_updates]" value="off" id="theme_updates_off" <?php checked( 'on', $options[ 'theme_updates' ] ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="theme_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		</div><!-- .dashboard-item-choice -->
            		</div><!-- dashboard-item-->
            		<div class="dashboard-item" "dashboard-main">
                		<div class="dashboard-item-header"><?php esc_html_e( 'All Translation Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                		</div><!-- .dashboard-item-header -->
                		<div class="dashboard-item-choice">
                    		
            				<input type="hidden" name="options[translation_updates]" value="on" />
            				<input type="radio" class="dashboard-hide" name="options[translation_updates]" value="off" id="translation_updates_off" <?php checked( 'on', $options[ 'translation_updates' ] ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="translation_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		</div><!-- .dashboard-item-choice -->
            		</div><!-- dashboard-item-->
        		</div><!-- .dashboard-item-wrapper -->
    		</div><!--- .dashboard-main-wrapper -->
		</div><!-- #dashboard-main-outputs -->
        <?php		
		return;
	} //end tab_output_plugins
}