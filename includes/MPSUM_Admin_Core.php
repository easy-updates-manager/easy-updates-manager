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
class MPSUM_Admin_Core {
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
		add_action( 'mpsum_admin_tab_main', array( $this, 'tab_output' ) );
		add_action( 'admin_init', array( $this, 'maybe_save_options' ) );
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
	public static function get_defaults() {

    	/**
		 * Filter the default plugin configuration.
		 *
		 * @since 6.0.5
		 *
		 * @param array    associative array of options
		 */
		return (array) apply_filters( 'mpsum_default_options', array(
			'all_updates'                                  => 'on',
			'core_updates'                                 => 'on',
			'plugin_updates'                               => 'on',
			'theme_updates'                                => 'on',
			'translation_updates'                          => 'on',
			'automatic_development_updates'                => 'off',
			'automatic_major_updates'                      => 'off',
			'automatic_minor_updates'                      => 'on',
			'automatic_plugin_updates'                     => 'default',
			'automatic_theme_updates'                      => 'default',
			'automatic_translation_updates'                => 'on',
			'notification_core_update_emails'              => 'on',
			'misc_browser_nag'                             => 'on',
			'misc_wp_footer'                               => 'on',
			'notification_core_update_emails_plugins'      => 'on',
			'notification_core_update_emails_themes'       => 'on',
			'notification_core_update_emails_translations' => 'on',
			'logs'                                         => 'off',
			'email_addresses'                              => array(),
		) );
	}

	/**
	* Determine whether the save the main options or not.
	*
	* Determine whether the save the main options or not.
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
		if ( !isset( $_REQUEST[ 'action' ] ) ) return;
		if ( !isset( $_POST[ 'options' ] ) ) return;
		if ( 'mpsum_save_core_options' !== $_REQUEST[ 'action' ] ) return;
		check_admin_referer( 'mpsum_main_update', '_mpsum' );

		$query_args = array();
		$query_args[ 'updated' ] = "1";
		$query_args[ 'tab' ] = 'main';
		
		 

		//Save options
		$options = $_POST[ 'options' ];
		if ( isset( $_POST[ 'reset' ] ) ) {
			$options = self::get_defaults();
		}
		
		$options_to_save = array();
		
		//Check for valid e-mail address
		if ( isset( $options[ 'email_addresses' ] ) ) {
			$email_addresses = explode( ',', $options[ 'email_addresses' ] );
			$email_addresses_to_save = array();
			if ( count( $email_addresses ) > 0 ) {
				foreach( $email_addresses as $email ) {
					if ( ! is_email( $email ) && ! empty( $email ) ) {
						$email_addresses_to_save = array();
						$query_args[ 'bad_email' ] = 1;
						break;
					} else {
						if ( ! empty( $email ) ) {
							$email_addresses_to_save[] = $email;
						}
						
					}
				}
			}
			$options_to_save[ 'email_addresses' ] = $email_addresses_to_save;
		}
				
		foreach( $options as $key => $value ) {
			if ( 'email_addresses' == $key ) continue;
			
			$option_value = sanitize_text_field( $value );
			$options_to_save[ sanitize_key( $key ) ] = $option_value;
		}

		MPSUM_Updates_Manager::update_options( $options_to_save, 'core' );

		//Redirect back to settings screen
		wp_redirect( esc_url_raw( add_query_arg( $query_args, MPSUM_Admin::get_url() ) ) );
		exit;
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
		$options = wp_parse_args( $options, self::get_defaults() );

		if ( isset( $_GET[ 'updated' ] ) ) {
			$message = __( 'Options saved.', 'stops-core-theme-and-plugin-updates' );
			?>
			<br />
			<div class="updated"><p><strong><?php echo esc_html( $message ); ?></strong></p></div>
			<?php 
			if ( isset( $_GET[ 'bad_email' ] ) ) {
				?>
				<div class="error"><p><strong><?php echo esc_html__( 'The email address is not valid', 'stops-core-theme-and-plugin-updates' ); ?></strong></p></div>
				<?php
			}
		}

		?>
        <form action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="post">
		<?php
		$logs = isset( $options[ 'logs' ] ) ? $options[ 'logs' ] : 'off';	
		?>
		<input type="hidden" name="options[logs]" value="<?php echo esc_attr( $logs ); ?>" />
		<h3><?php esc_html_e( 'Global Settings', 'stops-core-theme-and-plugin-updates' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'All Updates', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[all_updates]" value="on" id="all_updates_on" <?php checked( 'on', $options[ 'all_updates' ] ); ?> />&nbsp;<label for="all_updates_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[all_updates]" value="off" id="all_updates_off" <?php checked( 'off', $options[ 'all_updates' ] ); ?> />&nbsp;<label for="all_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p class="description"><?php esc_html_e( 'If this option is disabled, this will override all settings.', 'stops-core-theme-and-plugin-updates' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'WordPress Core Updates', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[core_updates]" value="on" id="core_updates_on" <?php checked( 'on', $options[ 'core_updates' ] ); ?> />&nbsp;<label for="core_updates_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[core_updates]" value="off" id="core_updates_off" <?php checked( 'off', $options[ 'core_updates' ] ); ?> />&nbsp;<label for="core_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p class="description"><?php esc_html_e( 'Prevents WordPress from showing it needs to be updated.', 'stops-core-theme-and-plugin-updates' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'All Plugin Updates', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[plugin_updates]" value="on" id="plugin_updates_on" <?php checked( 'on', $options[ 'plugin_updates' ] ); ?> />&nbsp;<label for="plugin_updates_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label><br />
					<p><input type="radio" name="options[plugin_updates]" value="off" id="plugin_updates_off" <?php checked( 'off', $options[ 'plugin_updates' ] ); ?> />&nbsp;<label for="plugin_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'All Theme Updates', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[theme_updates]" value="on" id="theme_updates_on" <?php checked( 'on', $options[ 'theme_updates' ] ); ?> />&nbsp;<label for="theme_updates_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[theme_updates]" value="off" id="theme_updates_off" <?php checked( 'off', $options[ 'theme_updates' ] ); ?> />&nbsp;<label for="theme_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'All Translation Updates', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[translation_updates]" value="on" id="translation_updates_on" <?php checked( 'on', $options[ 'translation_updates' ] ); ?> />&nbsp;<label for="translation_updates_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[translation_updates]" value="off" id="translation_updates_off" <?php checked( 'off', $options[ 'translation_updates' ] ); ?> />&nbsp;<label for="translation_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
				</td>
			</tr>
		</table>
		<h3><?php esc_html_e( 'Automatic Updates', 'stops-core-theme-and-plugin-updates' ); ?></h3>
		<p><?php esc_html_e( 'These options will enable or disable automatic updates (background updates) of certain parts of WordPress.', 'stops-core-theme-and-plugin-updates' ); ?></p>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Major Releases', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[automatic_major_updates]" value="on" id="automatic_major_on" <?php checked( 'on', $options[ 'automatic_major_updates' ] ); ?> />&nbsp;<label for="automatic_major_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[automatic_major_updates]" value="off" id="automatic_major_off" <?php checked( 'off', $options[ 'automatic_major_updates' ] ); ?> />&nbsp;<label for="automatic_major_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p class="description"><?php esc_html_e( 'Automatically update to major releases (e.g., 4.1, 4.2, 4.3).', 'stops-core-theme-and-plugin-updates' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Minor Releases', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[automatic_minor_updates]" value="on" id="automatic_minor_on" <?php checked( 'on', $options[ 'automatic_minor_updates' ] ); ?> />&nbsp;<label for="automatic_minor_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label><br />
					<p><input type="radio" name="options[automatic_minor_updates]" value="off" id="automatic_minor_off" <?php checked( 'off', $options[ 'automatic_minor_updates' ] ); ?> />&nbsp;<label for="automatic_minor_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
					<p class="description"><?php esc_html_e( 'Automatically update to minor releases (e.g., 4.1.1, 4.1.2, 4.1.3).', 'stops-core-theme-and-plugin-updates' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Development Updates', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[automatic_development_updates]" value="on" id="automatic_dev_on" <?php checked( 'on', $options[ 'automatic_development_updates' ] ); ?> />&nbsp;<label for="automatic_dev_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[automatic_development_updates]" value="off" id="automatic_dev_off" <?php checked( 'off', $options[ 'automatic_development_updates' ] ); ?> />&nbsp;<label for="automatic_dev_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p class="description"><?php esc_html_e( 'Update automatically to Bleeding Edge releases.', 'stops-core-theme-and-plugin-updates' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Automatic Plugin Updates', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[automatic_plugin_updates]" value="on" id="automatic_plugin_on" <?php checked( 'on', $options[ 'automatic_plugin_updates' ] ); ?> />&nbsp;<label for="automatic_plugin_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[automatic_plugin_updates]" value="off" id="automatic_plugin_off" <?php checked( 'off', $options[ 'automatic_plugin_updates' ] ); ?> />&nbsp;<label for="automatic_plugin_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[automatic_plugin_updates]" value="default" id="automatic_plugin_default" <?php checked( 'default', $options[ 'automatic_plugin_updates' ] ); ?> />&nbsp;<label for="automatic_plugin_default"><?php esc_html_e( 'Default', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[automatic_plugin_updates]" value="individual" id="automatic_plugin_individual" <?php checked( 'individual', $options[ 'automatic_plugin_updates' ] ); ?> />&nbsp;<label for="automatic_plugin_individual"><?php esc_html_e( 'Select Individually', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p class="description"><?php esc_html_e( 'Automatically update your plugins.  Select always on, always off, the WordPress default, or select plugins individually.', 'stops-core-theme-and-plugin-updates' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Automatic Theme Updates', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[automatic_theme_updates]" value="on" id="automatic_theme_on" <?php checked( 'on', $options[ 'automatic_theme_updates' ] ); ?> />&nbsp;<label for="automatic_theme_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[automatic_theme_updates]" value="off" id="automatic_theme_off" <?php checked( 'off', $options[ 'automatic_theme_updates' ] ); ?> />&nbsp;<label for="automatic_theme_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[automatic_theme_updates]" value="default" id="automatic_theme_default" <?php checked( 'default', $options[ 'automatic_theme_updates' ] ); ?> />&nbsp;<label for="automatic_theme_default"><?php esc_html_e( 'Default', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[automatic_theme_updates]" value="individual" id="automatic_theme_individual" <?php checked( 'individual', $options[ 'automatic_theme_updates' ] ); ?> />&nbsp;<label for="automatic_theme_individual"><?php esc_html_e( 'Select Individually', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p class="description"><?php esc_html_e( 'Automatically update your themes. Select always on, always off, the WordPress default, or select themes individually.', 'stops-core-theme-and-plugin-updates' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Translation Updates', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[automatic_translation_updates]" value="on" id="automatic_translation_on" <?php checked( 'on', $options[ 'automatic_translation_updates' ] ); ?> />&nbsp;<label for="automatic_translation_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[automatic_translation_updates]" value="off" id="automatic_translation_off" <?php checked( 'off', $options[ 'automatic_translation_updates' ] ); ?> />&nbsp;<label for="automatic_translation_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p class="description"><?php esc_html_e( 'Automatically update your translations.', 'stops-core-theme-and-plugin-updates' ); ?></p>
				</td>
			</tr>
		</table>
		<h3><?php esc_html_e( 'Notifications', 'stops-core-theme-and-plugin-updates' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Core E-mails', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
    				<input type="hidden" name="options[notification_core_update_emails]" value="off" />
					<p><input type="checkbox" name="options[notification_core_update_emails]" value="on" id="notification_core_update_emails_on" <?php checked( 'on', $options[ 'notification_core_update_emails' ] ); ?> />&nbsp;<label for="notification_core_update_emails_on"><?php esc_html_e( 'Core Update Emails', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<?php /* Hidden checkboxes until changes make into core. Shooting for WordPress 4.5 */ ?>
					<input type="hidden" name="options[notification_core_update_emails_plugins]" value="on" />
					<input type="hidden" name="options[notification_core_update_emails_themes]" value="on" />
                    <input type="hidden" name="options[notification_core_update_emails_translations]" value="on" />
                    <?php /*
                    <br />
                    <input type="checkbox" name="options[notification_core_update_emails_plugins]" value="on" id="notification_core_update_emails_plugins_on" <?php checked( 'on', $options[ 'notification_core_update_emails_plugins' ] ); ?> />&nbsp;<label for="notification_core_update_emails_plugins_on">
                    <?php esc_html_e( 'Core Plugin Emails', 'stops-core-theme-and-plugin-updates' ); ?></label><br />
					<input type="checkbox" name="options[notification_core_update_emails_themes]" value="on" id="notification_core_update_emails_themes" <?php checked( 'on', $options[ 'notification_core_update_emails_themes' ] ); ?> />&nbsp;<label for="notification_core_update_emails_themes"><?php esc_html_e( 'Core Theme Emails', 'stops-core-theme-and-plugin-updates' ); ?></label><br />
					<input type="checkbox" name="options[notification_core_update_emails_translations]" value="on" id="notification_core_update_emails_translations_on" <?php checked( 'on', $options[ 'notification_core_update_emails_translations' ] ); ?> />&nbsp;<label for="notification_core_update_emails_translations_on"><?php esc_html_e( 'Core Translation Emails', 'stops-core-theme-and-plugin-updates' ); ?></label>
					<p class="description"><?php esc_html_e( 'Disable e-mails that are sent when your site has been upgraded automatically. These will not be functional until WordPress 4.5.', 'stops-core-theme-and-plugin-updates' ); ?></p>*/ ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Notification E-mail', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<?php
					$email_addresses = array();
					if ( isset( $options[ 'email_addresses' ] ) && is_array( $options[ 'email_addresses' ] ) ) {
						foreach( $options[ 'email_addresses' ] as $email ) {
							if ( ! is_email( $email ) ) {
								$email_addresses = array();
								break;
							} else {
								$email_addresses[] = $email;
							}
						}
						if ( ! empty( $email_addresses ) ) {
							$email_addresses = implode( ',', $options[ 'email_addresses' ] );
						} else {
							$email_addresses = '';
						}
					}	
					?>
    				<input type="text" name="options[email_addresses]" value="<?php echo esc_attr( $email_addresses ); ?>" style="width: 50%" /><br />
    				<p class="description"><?php echo esc_html_e( 'Emails can be comma separated', 'stops-core-theme-and-plugin-updates' ); ?></p>
				</td>
			</tr>
		</table>
		<h3><?php esc_html_e( 'Miscellaneous', 'stops-core-theme-and-plugin-updates' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Browser Nag', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[misc_browser_nag]" value="on" id="misc_browser_nag_on" <?php checked( 'on', $options[ 'misc_browser_nag' ] ); ?> />&nbsp;<label for="misc_browser_nag_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[misc_browser_nag]" value="off" id="misc_browser_nag_off" <?php checked( 'off', $options[ 'misc_browser_nag' ] ); ?> />&nbsp;<label for="misc_browser_nag_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p class="description"><?php esc_html_e( 'Removes the browser nag for people using older browsers.', 'stops-core-theme-and-plugin-updates' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'WordPress Version in Footer', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<p><input type="radio" name="options[misc_wp_footer]" value="on" id="misc_wp_footer_on" <?php checked( 'on', $options[ 'misc_wp_footer' ] ); ?> />&nbsp;<label for="misc_wp_footer_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p><input type="radio" name="options[misc_wp_footer]" value="off" id="misc_wp_footer_off" <?php checked( 'off', $options[ 'misc_wp_footer' ] ); ?> />&nbsp;<label for="misc_wp_footer_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label></p>
					<p class="description"><?php esc_html_e( 'Removes the WordPress version in the footer.', 'stops-core-theme-and-plugin-updates' ); ?></p>
				</td>
			</tr>
		</table>
		<input type="hidden" name="action" value='mpsum_save_core_options' />
	    <?php
		wp_nonce_field( 'mpsum_main_update', '_mpsum' );
		echo '<p class="submit">';
		submit_button( __( 'Save Changes', 'stops-core-theme-and-plugin-updates' ) , 'primary', 'submit', false );
		echo '&nbsp;&nbsp;';
		submit_button( __( 'Reset to Defaults', 'stops-core-theme-and-plugin-updates' ) , 'secondary', 'reset', false );
		echo '</p>';
		?>
        </form>
    <?php
	} //end tab_output_plugins
}