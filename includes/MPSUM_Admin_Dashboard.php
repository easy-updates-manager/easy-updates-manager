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
		$options = MPSUM_Updates_Manager::get_options( 'core' );
		$options = $saved_options = wp_parse_args( $options, MPSUM_Admin_Core::get_defaults() );
		$disable_core_options = false;
        if( 'off' == $options[ 'all_updates' ] ) {
            $disable_core_options = true;
            $options[ 'core_updates' ] = 'off'; 
            $options[ 'plugin_updates' ] = 'off';
            $options[ 'theme_updates' ] = 'off';
            $options[ 'translation_updates' ] = 'off'; 
        }
		?>
		<form id="dashboard-form" method="post">
        <?php
        wp_nonce_field( 'mpsum_options_save', '_mpsum' );
        ?>
    	<div id="dashboard-main-outputs">
    		<div class="dashboard-main-wrapper main-updates">
        		<div class="dashboard-main-header"><?php esc_html_e( 'WordPress Updates', 'stops-core-theme-and-plugin-updates' ); ?></div><!-- .dashboard-main-header -->
        		<div class="dashboard-item-wrapper">
            		<div class="dashboard-item <?php if ( 'on' == $options[ 'all_updates' ] ) { echo 'active'; } ?>">
                		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'All Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                		</div><!-- .dashboard-item-header -->
                		<div class="dashboard-item-choice">
                            <input type="hidden" name="options[all_updates]" value="off"  />
                            <input type="checkbox"  data-context="core" data-action="all_updates" class="dashboard-hide" name="options[all_updates]" value="on" id="all_updates_off" <?php checked( 'on', $options[ 'all_updates' ] ); ?> />&nbsp;<label for="all_updates_off"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		</div><!-- .dashboard-item-choice -->
            		</div><!-- dashboard-item-->
            		<div class="dashboard-item <?php if ( 'on' == $options[ 'core_updates' ] ) { echo 'active'; } ?>">
                		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'WordPress Core Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                		</div><!-- .dashboard-item-header -->
                		<div class="dashboard-item-choice">
                    		<input type="hidden"   name="options[core_updates]" value="on" />
            				<input id="core_updates_off" data-context="core" data-action="core_updates" data-value="<?php echo esc_attr( $saved_options[ 'core_updates' ] ); ?>" type="checkbox"  class="dashboard-hide update-option" name="options[core_updates]" value="off"  <?php checked( 'on', $options[ 'core_updates' ] ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="core_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		</div><!-- .dashboard-item-choice -->
            		</div><!-- dashboard-item-->
            		<div class="dashboard-item <?php if ( 'on' == $options[ 'plugin_updates' ] ) { echo 'active'; } ?>">
                		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'All Plugin Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                		</div><!-- .dashboard-item-header -->
                		<div class="dashboard-item-choice">
                    		<input type="hidden" name="options[plugin_updates]" value="on" /> 
            				<input type="checkbox"  data-context="core" data-action="plugin_updates" data-value="<?php echo esc_attr( $saved_options[ 'plugin_updates' ] ); ?>" class="dashboard-hide update-option"  name="options[plugin_updates]" value="off" id="plugin_updates_off" <?php checked( 'on', $options[ 'plugin_updates' ] ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="plugin_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		</div><!-- .dashboard-item-choice -->
            		</div><!-- dashboard-item-->
            		<div class="dashboard-item <?php if ( 'on' == $options[ 'theme_updates' ] ) { echo 'active'; } ?>">
                		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'All Theme Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                		</div><!-- .dashboard-item-header -->
                		<div class="dashboard-item-choice">
                    		<input type="hidden" name="options[theme_updates]" value="on" />
            				<input  type="checkbox"  data-context="core" data-action="theme_updates" data-value="<?php echo esc_attr( $saved_options[ 'theme_updates' ] ); ?>" class="dashboard-hide update-option" name="options[theme_updates]" value="off" id="theme_updates_off" <?php checked( 'on', $options[ 'theme_updates' ] ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="theme_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		</div><!-- .dashboard-item-choice -->
            		</div><!-- dashboard-item-->
            		<div class="dashboard-item <?php if ( 'on' == $options[ 'translation_updates' ] ) { echo 'active'; } ?>">
                		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'All Translation Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                		</div><!-- .dashboard-item-header -->
                		<div class="dashboard-item-choice">
            				<input type="hidden" name="options[translation_updates]" value="on" />
            				<input id="translation_updates_off"   data-context="core" data-action="translation_updates" data-value="<?php echo esc_attr( $saved_options[ 'translation_updates' ] ); ?>" type="checkbox" class="dashboard-hide update-option" name="options[translation_updates]" value="off" <?php checked( 'on', $options[ 'translation_updates' ] ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="translation_updates_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		</div><!-- .dashboard-item-choice -->
            		</div><!-- dashboard-item-->
        		</div><!-- .dashboard-item-wrapper -->
        		</div><!--- .dashboard-main-wrapper -->
        		
        		<div class="dashboard-main-wrapper">
            		<div class="dashboard-main-header"><?php esc_html_e( 'Automatic Updates', 'stops-core-theme-and-plugin-updates' ); ?></div><!-- .dashboard-main-header -->
            		  <div class="dashboard-item-wrapper">
                		<div class="dashboard-item  <?php if( 'on' == $options[ 'automatic_major_updates' ] ) { echo 'active'; }?>">
                    		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'Major Releases', 'stops-core-theme-and-plugin-updates' ); ?>
                    		</div><!-- .dashboard-item-header -->
                    		<div class="dashboard-item-choice">
                                <input type="hidden" name="options[automatic_major_updates]" value="off"  />
                                <input type="checkbox"  data-context="core" data-action="automatic_major_updates" class="dashboard-hide" name="options[automatic_major_updates]" value="on" id="automatic_major_on" <?php checked( 'on', $options[ 'automatic_major_updates' ] ); ?> />&nbsp;<label for="automatic_major_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                    		</div><!-- .dashboard-item-choice -->
                		</div><!-- dashboard-item-->
                		<div class="dashboard-item   <?php if( 'on' == $options[ 'automatic_minor_updates' ] ) { echo 'active'; }?>">
                    		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'Minor Releases', 'stops-core-theme-and-plugin-updates' ); ?>
                    		</div><!-- .dashboard-item-header -->
                    		<div class="dashboard-item-choice">
                        		<input type="hidden"   name="options[automatic_minor_updates]" value="on" />
                				<input id="automatic_minor_on" data-context="core" data-action="automatic_minor_updates" type="checkbox"  class="dashboard-hide update-option" name="options[automatic_minor_updates]" value="off"  <?php checked( 'on', $options[ 'automatic_minor_updates' ] ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="automatic_minor_on"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                    		</div><!-- .dashboard-item-choice -->
                		</div><!-- dashboard-item-->
                		<div class="dashboard-item <?php if( 'on' == $options[ 'automatic_development_updates' ] ) { echo 'active'; }?>">
                    		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'Development Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                    		</div><!-- .dashboard-item-header -->
                    		<div class="dashboard-item-choice">
                        		<input type="hidden" name="options[automatic_development_updates]" value="on" /> 
                				<input id="automatic_dev_on" type="checkbox"  data-context="core" data-action="automatic_development_updates" class="dashboard-hide update-option"  name="options[automatic_development_updates]" value="off" id="plugin_updates_off" <?php checked( 'on', $options[ 'automatic_development_updates' ] ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="automatic_dev_on"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                    		</div><!-- .dashboard-item-choice -->
                		</div><!-- dashboard-item-->
                		<div class="dashboard-item <?php if( 'on' == $options[ 'automatic_translation_updates' ] ) { echo 'active'; }?>">
                    		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'Translation Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                    		</div><!-- .dashboard-item-header -->
                    		<div class="dashboard-item-choice">
                        		<input type="hidden" name="options[automatic_translation_updates]" value="on" />
                				<input type="checkbox"  data-context="core" data-action="automatic_translation_updates" class="dashboard-hide update-option" name="options[automatic_translation_updates]" value="off" id="automatic_translation_updates_on" <?php checked( 'on', $options[ 'automatic_translation_updates' ] ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="automatic_translation_updates_on"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                    		</div><!-- .dashboard-item-choice -->
                		</div><!-- dashboard-item-->
                		<div class="dashboard-item <?php if( 'off' != $options[ 'automatic_plugin_updates' ] ) { echo 'active'; }?>">
                    		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'Automatic Plugin Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                    		</div><!-- .dashboard-item-header -->
                    		<div class="multi-choice">
                        		<input type="radio" data-context="core" data-action="automatic_plugin_updates" name="options[automatic_plugin_updates]" value="on" id="automatic_plugin_on" <?php checked( 'on', $options[ 'automatic_plugin_updates' ] ); ?> />&nbsp;<label for="automatic_plugin_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label><br />
            					<input type="radio"  data-context="core" data-action="automatic_plugin_updates" name="options[automatic_plugin_updates]" value="off" id="automatic_plugin_off" <?php checked( 'off', $options[ 'automatic_plugin_updates' ] ); ?> />&nbsp;<label for="automatic_plugin_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label><br />
            					<input type="radio"  data-context="core" data-action="automatic_plugin_updates" name="options[automatic_plugin_updates]" value="default" id="automatic_plugin_default" <?php checked( 'default', $options[ 'automatic_plugin_updates' ] ); ?> />&nbsp;<label for="automatic_plugin_default"><?php esc_html_e( 'Default', 'stops-core-theme-and-plugin-updates' ); ?></label><br />
            					<input type="radio"  data-context="core" data-action="automatic_plugin_updates" name="options[automatic_plugin_updates]" value="individual" id="automatic_plugin_individual" <?php checked( 'individual', $options[ 'automatic_plugin_updates' ] ); ?> />&nbsp;<label for="automatic_plugin_individual"><?php esc_html_e( 'Select Individually', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		    </div><!--multi-choice-->
                		</div><!-- .dashboard-item -->
                		<div class="dashboard-item <?php if( 'off' != $options[ 'automatic_theme_updates' ] ) { echo 'active'; }?>">
                    		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'Automatic Theme Updates', 'stops-core-theme-and-plugin-updates' ); ?>
                    		</div><!-- .dashboard-item-header -->
                    		<div class="multi-choice">
                        		<input type="radio" data-context="core" data-action="automatic_theme_updates" name="options[automatic_theme_updates]" value="on" id="automatic_theme_on" <?php checked( 'on', $options[ 'automatic_theme_updates' ] ); ?> />&nbsp;<label for="automatic_theme_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label><br />
        					<input type="radio" data-context="core" data-action="automatic_theme_updates" name="options[automatic_theme_updates]" value="off" id="automatic_theme_off" <?php checked( 'off', $options[ 'automatic_theme_updates' ] ); ?> />&nbsp;<label for="automatic_theme_off"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label><br />
        					<input type="radio" data-context="core" data-action="automatic_theme_updates" name="options[automatic_theme_updates]" value="default" id="automatic_theme_default" <?php checked( 'default', $options[ 'automatic_theme_updates' ] ); ?> />&nbsp;<label for="automatic_theme_default"><?php esc_html_e( 'Default', 'stops-core-theme-and-plugin-updates' ); ?></label><br />
        					<input type="radio"  data-context="core" data-action="automatic_theme_updates" name="options[automatic_theme_updates]" value="individual" id="automatic_theme_individual" <?php checked( 'individual', $options[ 'automatic_theme_updates' ] ); ?> />&nbsp;<label for="automatic_theme_individual"><?php esc_html_e( 'Select Individually', 'stops-core-theme-and-plugin-updates' ); ?></label>
                		    </div><!--multi-choice-->
                		</div><!-- .dashboard-item -->
                </div>
        		</div>
        		<!-- Plugin / Theme Updates -->
        		<div class="dashboard-main-wrapper dashboard-plugin-theme-updates">
            		<div class="dashboard-main-header"><?php esc_html_e( 'Plugin and Theme Updates', 'stops-core-theme-and-plugin-updates' ); ?></div><!-- .dashboard-main-header -->
            		<div class="dashboard-tab">
            		    <div class="dashboard-tab-header-plugin dashboard-tab-item active" ><a href="#" data-tab-action="plugins" ><?php esc_html_e( 'Plugin Updates', 'stops-core-theme-and-plugin-updates' ); ?></a></div>
            		    <div class="dashboard-tab-header-theme dashboard-tab-item" data-tab-plugins="plugins"><a href="#" data-tab-action="themes" ><?php esc_html_e( 'Theme Updates', 'stops-core-theme-and-plugin-updates' ); ?></a></div>
            		</div><!- .dashboard-tab -->
            		<div class="dashboard-tab-plugins  dashboard-tab-content active">
                		<div class="dashboard-item-wrapper">
                    		<?php
                            $can_show_plugins = $can_show_themes = false;
                            if(  'on' == $options[ 'theme_updates' ] ) {
                                $can_show_themes = true;
                            }
                            if(  'on' == $options[ 'plugin_updates' ] ) {
                                $can_show_plugins = true;
                            }
                            ?>
                            <?php
                            if ( $can_show_plugins ) :
                                $options = MPSUM_Updates_Manager::get_options(  'plugins' );
                                $plugins = get_plugins(); 
                                foreach( $plugins as $plugin_slug => $plugin_data ) {
                                    $is_plugin_active = true;
                                    if ( in_array( $plugin_slug,  $options ) ) {
                                        $is_plugin_active = false;
                                    }
                                    $plugin_name = $plugin_data[ 'Name' ];
                                    ?>
                                    <div class="dashboard-item <?php echo $is_plugin_active ? 'active': '' ?>">
                                		<div class="dashboard-item-header input-radio"><?php echo esc_html( $plugin_name ) ?>
                                		</div><!-- .dashboard-item-header -->
                                		<div class="dashboard-item-choice">
                            				<input type="hidden" name="options[plugins]" value="<?php echo esc_attr( $plugin_slug ); ?> " />
                            				<input id="<?php echo esc_attr( $plugin_slug ); ?>-check" type="checkbox" data-context="plugins" data-action="<?php echo esc_attr( $plugin_slug ); ?>" class="dashboard-hide update-option" name="options[plugins]" value="<?php echo esc_attr( $plugin_slug ); ?>" id="<?php echo esc_attr( $plugin_slug ); ?>_off" <?php checked( true, $is_plugin_active ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="<?php echo esc_attr( $plugin_slug ); ?>-check"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                                		</div><!-- .dashboard-item-choice -->
                            		</div><!-- dashboard-item-->
                                    
                                    <?php
                                    }
                                else:
                                ?>
                                <p><?php 
                                    esc_html_e( 'All plugin updates have been disabled.', 'stops-core-theme-and-plugin-updates' ); 
                                endif;
                                ?>
                                </p>
                		</div><!-- .dashboard-item-wrapper -->
            		</div><!-- .dashboard-tab-plugins -->
            		<div class="dashboard-tab-themes dashboard-tab-content">
                		<div class="dashboard-item-wrapper">
                             <?php
                             if( $can_show_themes ) :
                                $options = MPSUM_Updates_Manager::get_options(  'themes' );
                                $themes = wp_get_themes();
                                
                                foreach( $themes as $theme_slug => $theme_data ) {
                                    $is_theme_active = true;
                                    if ( in_array( $theme_slug,  $options ) ) {
                                        $is_theme_active = false;
                                    }
                                    $theme_name = $theme_data->Name;
                                    ?>
                                    
                                    <div class="dashboard-item <?php echo $is_theme_active ? 'active': '' ?>">
                                		<div class="dashboard-item-header input-radio"><?php echo esc_html( $theme_name ) ?>
                                		</div><!-- .dashboard-item-header -->
                                		<div class="dashboard-item-choice">
                                    		<?php
                                            $checked_value = 'checked';
                                    		if ( in_array( $theme_slug,  $options )  ) {
                                        		$checked_value = '';
                                            } else {
                                            }
                                            
                                            ?>
                            				<input type="hidden" name="options[themes]" value="<?php echo esc_attr( $theme_slug ); ?> " />
                            				<input id="<?php echo esc_attr( $theme_slug ); ?>-check" type="checkbox" data-context="themes" data-action="<?php echo esc_attr( $theme_slug ); ?>" class="dashboard-hide update-option" name="options[themes]" value="<?php echo esc_attr( $theme_slug ); ?>" id="<?php echo esc_attr( $theme_slug ); ?>_off" <?php checked( true, $is_theme_active ); ?> <?php disabled( true, false ); ?> />&nbsp;<label for="<?php echo esc_attr( $theme_slug ); ?>-check"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                                		</div><!-- .dashboard-item-choice -->
                            		</div><!-- dashboard-item-->
                                    
                                    <?php
                                }
                            else: 
                                ?>
                                <p><?php 
                                esc_html_e( 'All theme updates have been disabled.', 'stops-core-theme-and-plugin-updates' ) ?>
                            <?php
                            endif;
                            ?>    
                		</div><!-- .dashboard-item-wrapper -->
            		</div><!-- .dashboard-tab-plugins -->
        		</div><!--- .dashboard-main-wrapper -->
        		
        		<!-- Plugin / Theme Updates -->
        		<div class="dashboard-main-wrapper dashboard-plugin-theme-auto-updates">
            		<div class="dashboard-main-header"><?php esc_html_e( 'Plugin and Theme Automatic Updates', 'stops-core-theme-and-plugin-updates' ); ?></div><!-- .dashboard-main-header -->
            		<div class="dashboard-tab">
            		    <div class="dashboard-tab-header-plugin dashboard-tab-item active" ><a href="#" data-tab-action="plugins" ><?php esc_html_e( 'Plugin Updates', 'stops-core-theme-and-plugin-updates' ); ?></a></div>
            		    <div class="dashboard-tab-header-theme dashboard-tab-item" data-tab-plugins="plugins"><a href="#" data-tab-action="themes" ><?php esc_html_e( 'Theme Updates', 'stops-core-theme-and-plugin-updates' ); ?></a></div>
            		</div><!- .dashboard-tab -->
            		<div class="dashboard-tab-plugins  dashboard-tab-content active">
                		<div class="dashboard-item-wrapper">
                    		<?php
                            $can_show_plugins = false;
                            $options = MPSUM_Updates_Manager::get_options(  'core' );
                            if ( !isset( $options[ 'automatic_plugin_updates' ] ) ) {
                                $options[ 'automatic_plugin_updates' ] = 'default';
                                $can_show_plugins = $options[ 'automatic_plugin_updates' ];
                            } else {
                                $can_show_plugins = $options[ 'automatic_plugin_updates' ];
                            }
                            if ( isset( $options[ 'plugin_updates' ] ) && 'off' == $options[ 'plugin_updates' ] ) {
                                $can_show_plugins = 'off';   
                            }
                            $error = '';
                            if ( 'default' == $can_show_plugins ) {
                                $can_show_plugins = false;
                                $error = __( 'WordPress defaults control which updates are automatic or not.', 'stops-core-theme-and-plugin-updates' );
                            } elseif ( 'on' == $can_show_plugins ) {
                                $can_show_plugins = false;
                                $error = __( 'Automatic updates are on for all plugins', 'stops-core-theme-and-plugin-updates' );
                            } elseif ( 'off' == $can_show_plugins ) {
                                $can_show_plugins = false;
                                $error = __( 'Automatic updates are disabled for all plugins', 'stops-core-theme-and-plugin-updates' );
                            } else {
                                $can_show_plugins = true;   
                            }
                            ?>
                                <?php
                                if ( $can_show_plugins ) :
                                    $options = MPSUM_Updates_Manager::get_options(  'plugins_automatic' );
                                    $plugins = get_plugins(); 
                                    foreach( $plugins as $plugin_slug => $plugin_data ) {
                                        $is_plugin_active = false;
                                        if ( in_array( $plugin_slug,  $options ) ) {
                                            $is_plugin_active = true;
                                        }
                                        $plugin_name = $plugin_data[ 'Name' ];
                                        ?>
                                        <div class="dashboard-item">
                                    		<div class="dashboard-item-header input-radio"><?php echo esc_html( $plugin_name ) ?>
                                    		</div><!-- .dashboard-item-header -->
                                    		<div class="dashboard-item-choice">
                                				<input type="hidden" name="options[plugins_automatic]" value="<?php echo esc_attr( $plugin_slug ); ?> " />
                                				<input id="update_<?php echo esc_attr( $plugin_slug ); ?>-check" type="checkbox" data-context="plugins_automatic" data-action="<?php echo esc_attr( $plugin_slug ); ?>" class="dashboard-hide update-option" name="options[plugins_automatic]" value="<?php echo esc_attr( $plugin_slug ); ?>" id="<?php echo esc_attr( $plugin_slug ); ?>_off" <?php checked( true, $is_plugin_active ); ?> <?php disabled( true, $disable_core_options ); ?> />&nbsp;<label for="update_<?php echo esc_attr( $plugin_slug ); ?>-check"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                                    		</div><!-- .dashboard-item-choice -->
                                		</div><!-- dashboard-item-->
                                        
                                        <?php
                                    }
                                else:
                                ?>
                                <p><?php 
                                   echo esc_html( $error ); 
                                   ?>
                                    </p>
                                    <?php
                                endif;
                                ?>
                		</div><!-- .dashboard-item-wrapper -->
            		</div><!-- .dashboard-tab-plugins -->
            		<div class="dashboard-tab-themes dashboard-tab-content">
                		<div class="dashboard-item-wrapper">
                                 <?php
                                $can_show_themes = false;
                                $options = MPSUM_Updates_Manager::get_options(  'core' );
                                if ( !isset( $options[ 'automatic_theme_updates' ] ) ) {
                                    $options[ 'automatic_theme_updates' ] = 'default';
                                    $auto_theme_updates = $options[ 'automatic_theme_updates' ];
                                } else {
                                    $auto_theme_updates = $options[ 'automatic_theme_updates' ];
                                }
                                 if ( isset( $options[ 'theme_updates' ] ) && 'off' == $options[ 'theme_updates' ] ) {
                                    $can_show_plugins = 'off';   
                                    $auto_theme_updates = 'off';
                                }
                                $error = '';
                                if ( 'default' == $auto_theme_updates ) {
                                    $can_show_themes = false;
                                    $error = __( 'WordPress defaults control which updates are automatic or not.', 'stops-core-theme-and-plugin-updates' );
                                } elseif ( 'on' == $auto_theme_updates ) {
                                    $can_show_themes = false;
                                    $error = __( 'Automatic updates are on for all themes', 'stops-core-theme-and-plugin-updates' );
                                } elseif ( 'off' == $auto_theme_updates ) {
                                    $can_show_themes = false;
                                    $error = __( 'Automatic updates are disabled for all themes', 'stops-core-theme-and-plugin-updates' );
                                } else {
                                    $can_show_themes = true;   
                                }
                                 if( $can_show_themes ) :
                                    $options = MPSUM_Updates_Manager::get_options( 'themes_automatic' );
                                    $themes = wp_get_themes();
                                                                
                                    foreach( $themes as $theme_slug => $theme_data ) {
                                        $is_theme_active = false;
                                        if ( in_array( $theme_slug,  $options ) ) {
                                            $is_theme_active = true;
                                        }
                                        $theme_name = $theme_data->Name;
                                        ?>
                                        <div class="dashboard-item">
                                    		<div class="dashboard-item-header input-radio"><?php echo esc_html( $theme_name ) ?>
                                    		</div><!-- .dashboard-item-header -->
                                    		<div class="dashboard-item-choice">
                                        		<?php
                                                $checked_value = 'checked';
                                        		if ( in_array( $theme_slug,  $options )  ) {
                                            		$checked_value = '';
                                                } else {
                                                }
                                                
                                                ?>
                                				<input type="hidden" name="options[themes_automatic]" value="<?php echo esc_attr( $theme_slug ); ?> " />
                                				<input id="themes_automatic_on_<?php echo $theme_slug; ?>" type="checkbox" data-context="themes_automatic" data-action="<?php echo esc_attr( $theme_slug ); ?>" class="dashboard-hide update-option" name="options[themes_automatic]" value="<?php echo esc_attr( $theme_slug ); ?>" id="<?php echo esc_attr( $theme_slug ); ?>_off" <?php checked( true, $is_theme_active ); ?> <?php disabled( true, false ); ?> />&nbsp;<label for="themes_automatic_on_<?php echo $theme_slug; ?>"><?php esc_html_e( 'Disabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                                    		</div><!-- .dashboard-item-choice -->
                                		</div><!-- dashboard-item-->
                                        
                                        <?php
                                    }
                                else: 
                                    ?>
                                    <p><?php 
                                   echo esc_html( $error ); 
                                   ?>
                                    </p>
                                <?php
                                endif;
                                ?>    
                		</div><!-- .dashboard-item-wrapper -->
            		</div><!-- .dashboard-tab-plugins -->
        		</div><!--- .dashboard-main-wrapper -->
        		<div class="dashboard-main-wrapper">
            		<div class="dashboard-main-header">
                		<?php
                        $options = MPSUM_Updates_Manager::get_options( 'core' );
		$options = wp_parse_args( $options, MPSUM_Admin_Core::get_defaults() );
                    		?>
                		<?php esc_html_e( 'WordPress Notifications', 'stops-core-theme-and-plugin-updates' ); ?></div><!-- .dashboard-main-header -->
            		<div class="dashboard-item-wrapper">
                		<div class="dashboard-item <?php if( 'on' == $options[ 'notification_core_update_emails' ] ) { echo 'active'; }?>">
                    		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'Core E-mails', 'stops-core-theme-and-plugin-updates' ); ?>
                    		</div><!-- .dashboard-item-header -->
                    		<div class="dashboard-item-choice">
                                <input type="hidden" name="options[notification_core_update_emails]" value="off"  />
                                <input type="checkbox"  data-context="core" data-action="notification_core_update_emails" class="dashboard-hide" name="options[notification_core_update_emails]" value="off" id="notification_core_update_emails_on" <?php checked( 'on', $options[ 'notification_core_update_emails' ] ); ?> />&nbsp;<label for="notification_core_update_emails_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                    		</div><!-- .dashboard-item-choice -->
                		</div><!-- dashboard-item-->
                		<?php
                        /* To be added pre 4.5 assuming filter is in place */
                        /*
                		<div class="dashboard-item <?php if( 'on' == $options[ 'notification_core_update_emails_plugins' ] ) { echo 'active'; }?>">
                    		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'Core Plugin Emails', 'stops-core-theme-and-plugin-updates' ); ?>
                    		</div><!-- .dashboard-item-header -->
                    		<div class="dashboard-item-choice">
                                <input type="checkbox" name="options[notification_core_update_emails_plugins]" value="off"  />
                                <input type="checkbox"  data-context="core" data-action="notification_core_update_emails_plugins" class="dashboard-hide" name="options[all_updates]" value="on" id="notification_core_update_emails_plugins_on" <?php checked( 'on', $options[ 'notification_core_update_emails_plugins' ] ); ?> />&nbsp;<label for="notification_core_update_emails_plugins_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                    		</div><!-- .dashboard-item-choice -->
                		</div><!-- dashboard-item-->
                		<div class="dashboard-item <?php if( 'on' == $options[ 'notification_core_update_emails_themes' ] ) { echo 'active'; }?>">
                    		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'Core Theme Emails', 'stops-core-theme-and-plugin-updates' ); ?>
                    		</div><!-- .dashboard-item-header -->
                    		<div class="dashboard-item-choice">
                                <input type="checkbox" name="options[notification_core_update_emails_themes]" value="off"  />
                                <input type="checkbox"  data-context="core" data-action="notification_core_update_emails_themes" class="dashboard-hide" name="options[notification_core_update_emails_themes]" value="on" id="notification_core_update_emails_themes_on" <?php checked( 'on', $options[ 'notification_core_update_emails_themes' ] ); ?> />&nbsp;<label for="notification_core_update_emails_themes_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                    		</div><!-- .dashboard-item-choice -->
                		</div><!-- dashboard-item-->
                		<div class="dashboard-item <?php if( 'on' == $options[ 'notification_core_update_emails_translations' ] ) { echo 'active'; }?>">
                    		<div class="dashboard-item-header input-radio"><?php esc_html_e( 'Core Translation Emails', 'stops-core-theme-and-plugin-updates' ); ?>
                    		</div><!-- .dashboard-item-header -->
                    		<div class="dashboard-item-choice">
                        		
                                <input type="checkbox" name="options[notification_core_update_emails_translations]" value="off"  />
                                <input type="checkbox"  data-context="core" data-action="notification_core_update_emails_translations" class="dashboard-hide" name="options[notification_core_update_emails_translations]" value="on" id="notification_core_update_emails_translations_on" <?php checked( 'on', $options[ 'notification_core_update_emails_translations' ] ); ?> />&nbsp;<label for="notification_core_update_emails_translations_on"><?php esc_html_e( 'Enabled', 'stops-core-theme-and-plugin-updates' ); ?></label>
                    		</div><!-- .dashboard-item-choice -->
                		</div><!-- dashboard-item-->
                		*/ ?>
            		</div><!-- .dashboard-item-wrapper -->
        		</div><!--- .dashboard-main-wrapper -->
        	
		</div><!-- #dashboard-main-outputs -->
		</form>
        <?php		
		return;
	} //end tab_output_plugins
}