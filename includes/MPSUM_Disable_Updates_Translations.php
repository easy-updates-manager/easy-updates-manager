<?php
/**
 * Disables all WordPress translation updates.
 *
 * Disables all WordPress translation updates.
 *
 * @since 5.0.0
 *
 * @package WordPress
 */
//Credit - From https://wordpress.org/plugins/disable-wordpress-updates/
class MPSUM_Disable_Updates_Translations {
	
	public function __construct() {
		

		/*
		 * Disable All Automatic Updates
		 * 3.7+
		 * 
		 * @author	sLa NGjI's @ slangji.wordpress.com
		 */
		add_filter( 'auto_update_translation', '__return_false' );
		
		/*
		 * Disable Theme Translations
		 * 2.8 to 3.0
		 */
		add_filter( 'transient_update_themes', array( $this, 'remove_translations' ) );
		/*
		 * 3.0
		 */
		add_filter( 'site_transient_update_themes', array( $this, 'remove_translations' ) );
		
		
		/*
		 * Disable Plugin Translations
		 * 2.8 to 3.0
		 */
		add_action( 'transient_update_plugins', array( $this, 'remove_translations' ) );
		/*
		 * 3.0
		 */
		add_filter( 'site_transient_update_plugins', array( $this, 'remove_translations' ) );
		
		
		/*
		 * Disable Core Translations
		 * 2.8 to 3.0
		 */
		add_filter( 'transient_update_core', array( $this, 'remove_translations' ) );
		/*
		 * 3.0
		 */
		add_filter( 'site_transient_update_core', array( $this, 'remove_translations' ) );
		
	} //end constructor
	
	
	public function remove_translations( $transient ) {
		
		if ( is_object( $transient ) && isset( $transient->translations ) ) {
			$transient->translations = array();	
		}
		return $transient;
	}
	
}