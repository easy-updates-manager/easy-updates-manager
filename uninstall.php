<?php
/**
* Uninstall script
*
* Uninstall script for Easy Updates Manager.
*
* @since 5.0.0
*
* @package WordPress
*/
if (!defined( 'WP_UNINSTALL_PLUGIN' )) {
    exit ();
} 
delete_option( '_disable_updates' );
delete_site_option( '_disable_updates' );
delete_option( 'MPSUM' );
delete_site_option( 'MPSUM' );